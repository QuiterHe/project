/**
 * @author hezhang
 * @time  2017-2-17 16:01:24
 * @brief 一个基于libevent的server端例子（数据回显）
 * */

#include <stdio.h>
#include <stdlib.h>
#include <errno.h>
#include <assert.h>
#include <string.h>
#include <event.h>
#include <sys/types.h>
#include <unistd.h>
#include <netinet/in.h>
#include <sys/socket.h>
#include <event2/event.h>
#include <event2/bufferevent.h>

#define PORT 6666
#define BACKLOG 5
#define MEM_SIZE 1024

struct event_base* base;
struct sock_ev {
    struct event* read_ev;
    struct event* write_ev;
    char* buffer;
};

void release_sock_event(struct sock_ev* ev)
{
    event_del(ev->read_ev);
    free(ev->read_ev);
    free(ev->write_ev);
    free(ev->buffer);
    free(ev);
}

void on_accept(int sock, short event, void* arg);
void on_read(int sock, short event, void* arg);
void on_write(int sock, short event, void* arg);

int main(int argc, char* argv[])
{
    struct sockaddr_in my_addr;
    int sock;
    //创建套接字描述符，实质是一个文件描述符
    //AF_INET表示使用IP地址，SOCK_STREAM表示使用流式套接字
    sock = socket(AF_INET, SOCK_STREAM, 0);
    int yes = 1;
    setsockopt(sock, SOL_SOCKET, SO_REUSEADDR, &yes, sizeof(int));
    memset(&my_addr, 0, sizeof(my_addr));

    //实例化对象的属性
    my_addr.sin_family = AF_INET;
    my_addr.sin_port = htons(PORT);
    my_addr.sin_addr.s_addr = INADDR_ANY;

    //将套接字地址和套接字描述符绑定起来
    bind(sock, (struct sockaddr*)&my_addr, sizeof(struct sockaddr));
    //监听该套接字，连接的客户端数量最多为BACKLOG
    listen(sock, BACKLOG);

    //声明事件
    struct event listen_ev;
    //创建基事件
    base = event_base_new();
    //设置回调函数.将event对象监听的socket托管给event_base,指定要监听的事件类型，并绑上相应的回调函数
    event_set(&listen_ev, sock, EV_READ|EV_PERSIST, on_accept, NULL);//
    //上述操作说明在listen_ev这个事件监听sock这个描述字的读操作(EV_READ)，当读消息到达则调用on_accept函数，EV_PERSIST参数告诉系统持续的监听sock上的读事件，
    //不指定这个属性的话，回调函数被触发后,事件会被删除.所以,如果不加该参数，每次要监听该事件时就要重复的调用event_add函数，从前面的代码可知，
    //sock这个描述字是bind到本地的socket端口上，因此其对应的可读事件自然就是来自客户端的连接到达，我们就可以调用accept无阻塞的返回客户的连接了。

    //使从属于基事件.将listen_ev注册到base这个事件中，相当于告诉处理IO的管家请留意我的listen_ev上的事件。
    event_base_set(base, &listen_ev);
    //有时候看到使用<span style="color:#FF0000;">event_new</span>（base, listener, EV_READ|EV_PERSIST, do_accept, (void*)base）代替event_set和event_base_set这两个函数

    //添加到事件队列当中.相当于告诉处理IO的管家，当有我的事件到达时你发给我(调用on_accept函数)，至此对listen_ev的初始化完毕
    event_add(&listen_ev, NULL);
    //开始循环.正式启动libevent的事件处理机制，使系统运行起来.event_base_dispatch是一个无限循环
    event_base_dispatch(base);
    return 0;
}

void on_accept(int sock, short event, void* arg)
{
    struct sockaddr_in cli_addr;
    int newfd;
    socklen_t sin_size;
    // read_ev must allocate from heap memory, otherwise the program would crash from segmant fault
    struct event* read_ev = (struct event*)malloc(sizeof(struct event));
    sin_size = sizeof(struct sockaddr_in);
    newfd = accept(sock, (struct sockaddr*)&cli_addr, &sin_size);//指定服务端去接受客户端的连接
    //客户的描述字newfd上监听可读事件，当有数据到达是调用on_read函数
    event_set(read_ev, newfd, EV_READ|EV_PERSIST, on_read, read_ev);
    event_base_set(base, read_ev);
    event_add(read_ev, NULL);
    //这里需要注意两点，一是read_ev需要从堆里malloc出来，如果是在栈上分配，那么当函数返回时变量占用的内存会被释放，
    //因此事件主循环event_base_dispatch会访问无效的内存而导致进程崩溃(即crash)；第二个要注意的是event_set中,read_ev作为参数传递给了on_read函数
}

void on_read(int sock, short event, void* arg)
{
    struct event* write_ev;
    int size;
    char* buffer = (char*)malloc(MEM_SIZE);
    bzero(buffer, MEM_SIZE);
    size = recv(sock, buffer, MEM_SIZE, 0);
    printf("receive data:%s, size:%d\n", buffer, size);
    if (size == 0)//当从socket读返回0标志,对方已经关闭了连接，因此这个时候就没必要继续监听该套接口上的事件
    {
        event_del((struct event*)arg);
        //由于EV_READ在on_accept函数里是用EV_PERSIST参数注册的，因此要显示的调用event_del函数取消对该事件的监听
        free((struct event*)arg);
        close(sock);
        return;
    }
    write_ev = (struct event*) malloc(sizeof(struct event));
    event_set(write_ev, sock, EV_WRITE, on_write, buffer);//写时调用on_write函数，注意将buffer作为参数传递给了on_write
    event_base_set(base, write_ev);
    event_add(write_ev, NULL);
}
// on_write函数中向客户端回写数据，然后释放on_read函数中malloc出来的buffer。在很多书合编程指导中都很强调资源的所有权，经常要求谁分配资源、就由谁释放资源，
//这样对资源的管理指责就更明确，不容易出问题，但是通过该例子我们发现在异步编程中资源的分配与释放往往是由不同的所有者操作的，因此也是比较容易出问题的地方。
void on_write(int sock, short event, void* arg)
{
    char* buffer = (char*)arg;
    send(sock, buffer, strlen(buffer), 0);
    free(buffer);
}
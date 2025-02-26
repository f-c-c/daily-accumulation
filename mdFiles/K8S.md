# K8S(Kubernetes)

### Kubernetes 是什么？

> Kubernetes是Google开源的容器集群管理系统，是Google多年⼤规模容器管理技术Borg的开源版本
>
> Kubernetes，因为首尾字母中间有8个字符，所以被简写成 K8s
>
> K8s 是底层资源与容器间的一个抽象层，如果和单机架构类比，可以算 作是一个分布式时代的 Linux
>
> K8s在 Docker 技术的基础上，为 容器化的应用提供**部署运行**、**资源调度**、**服务发现**和**动态伸缩**等一系列 完整功能，提高了大规模容器集群管理的便捷性

### 特点：

> k8s是一个管理容器的工具，也是管理应用整个生命周期的一个工具， 从创建应用，应用的部署，应用提供服务，扩容缩容应用，应用更新， 而且可以做到故障自愈
>
> 可移植:支持公有云，私有云，混合云
> 可扩展:模块化，热插拨，可组合
>自愈:自动替换，自动重启，自动复制，自动扩展

### 相关概念

> 主机(Master):用于控制 Kubernetes 节点的计算机。所有任务分配都来自于此。 节点(Node):执行请求和分配任务的计算机。由 Kubernetes 主机负责对节点进行控制。
>
> 容器集(Pod):部署在单个节点上的，且包含一个或多个容器的容器组。同一容器集中的所有容器共 享同一个 IP 地址、IPC、主机名称及其它资源。容器集会将网络和存储从底层容器中抽象出来。这样， 您就能更加轻松地在集群中移动容器。
>
> 复制控制器(Replication controller): 用于控制应在集群某处运行的完全相同的容器集副本数量。
>
> 服务(Service):服务可将工作定义与容器集分离。Kubernetes 服务代理会自动将服务请求分配到正 确的容器集——无论这个容器集会移到集群中的哪个位置，即使它已被替换。
>
> Kubelet: 这是一个在节点上运行的服务，可读取容器清单，确保指定的容器启动并运行。 kubectl: Kubernetes 的命令行配置工具

### 相关资源

- 官网 https://kubernetes.io

- Chart 应用仓库 https://hub.kubeapps.com/

- 中文手册 https://www.kubernetes.org.cn/docs
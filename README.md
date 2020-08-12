# 日志中心搭建

## elk介绍
- elk是elastic公司提供的一套完整的日志收集和展示的解决方案，它是由三个产品的首字母缩写而成，分别是elasticSearch、logstash、kibana
  - elasticSearch： 简称 Es,它是一个分布式搜索引擎，可用于全文搜索、结构化搜索以及分析。它是一个建立在全文搜索引擎（Apache Lucene）基础上的一个搜索引擎。使用java语言编写
  - logstash：一个具有实时传输的数据收集引擎，用来进行数据收集（例如：读取文本文件）、解析、并将数据发送给es
  - Kibana：为es提供分析和可视化的web平台,它可以在es的索引中查找、交互数据、并生成各种维度的表格图形

## elk 用途

传统意义上，Elk是作为代替splunk的一个开源解决方案。Splunk是日志分析领域的领导者。日志分析不仅包括系统产生的错误日志、异常，也包括业务逻辑、任何文本类的分析，而基于日志分的分析，能够产生非常多的解决方案；比如：

1. 问题排查：我们常说，运维和开发这一辈子无非就是和问题在战斗，所以这个说起来很朴实的四个字，其实是沉甸甸的。很多公司其实不缺钱，就要稳定，而要稳定，就要运维和开发能够快速的定位问题，甚至防微杜渐，把问题杀死在摇篮里。日志分析技术显然问题排查的基石。基于日志做问题排查，还有一个很帅的技术，叫全链路追踪，比如阿里的eagleeye 或者Google的dapper，也算是日志分析技术里的一种
2. 监控和预警：日志，监控，预警是相辅相成的。基于日志的监控，预警使得运维有自己的机械战队，大大节省人力以及延长运维的寿命
3. 关联事件：多个数据源产生的日志进行联动分析，通过某种分析算法，就能够解决生活中各个问题。比如金融里的风险欺诈等。这个可以可以应用到无数领域了，取决于你的想象力
4. 数据分析：这个对于数据分析师，还有算法工程师都是有所裨益的

![image-20200811100802520](C:\Users\NO.01\AppData\Roaming\Typora\typora-user-images\image-20200811100802520.png)



## 安装脚本下载

- docker Registry Mirrors推荐

  ```json
  {
    "registry-mirrors": ["https://e2iytqf0.mirror.aliyuncs.com/"]
  }
  ```

  

下载地址：



## 设置权限

1. es启用xpack安全认证，配置文件添加配置  `xpack.security.enabled: true`

2. 执行命令重置密码 `elasticsearch-setup-passwords interactive`

   ```
   [root@5d89a0809d28 elasticsearch]# elasticsearch-setup-passwords interactive
   Initiating the setup of passwords for reserved users elastic,apm_system,kibana,kibana_system,logstash_system,beats_system,remote_monitoring_user.
   You will be prompted to enter passwords as the process progresses.
   Please confirm that you would like to continue [y/N]y
   
   
   Enter password for [elastic]: 
   Reenter password for [elastic]: 
   Enter password for [apm_system]: 
   Reenter password for [apm_system]: 
   Enter password for [kibana_system]: 
   Reenter password for [kibana_system]: 
   Enter password for [logstash_system]: 
   Reenter password for [logstash_system]: 
   Enter password for [beats_system]: 
   Reenter password for [beats_system]: 
   Enter password for [remote_monitoring_user]: 
   Reenter password for [remote_monitoring_user]: 
   Changed password for user [apm_system]
   Changed password for user [kibana_system]
   Changed password for user [kibana]
   Changed password for user [logstash_system]
   Changed password for user [beats_system]
   Changed password for user [remote_monitoring_user]
   Changed password for user [elastic]
   ```

   > 192.168.10.191初始密码：root123456

3. kibana设置用户密码，配置文件添加下面配置

   - 如果您不介意在配置文件中看到密码，请`kibana.yml`在Kibana目录中的文件中取消注释并更新以下设置

   ```yml
   #
   #
   #xpack配置参考:https://www.elastic.co/guide/en/kibana/7.9/security-settings-kb.html
   #kibana配置参考:https://www.elastic.co/guide/en/kibana/7.9/settings.html
   #
   server.name: kibana
   server.host: "0.0.0.0"
   elasticsearch.hosts: [ "http://es:9200" ]
   i18n.locale: zh-CN
   #elasticsearch.username: "kibana_system"
   #elasticsearch.password: "root123456"
   xpack.security.encryptionKey: "8884aea3bdfb65e3083a9280f4758086"
   ```

   > 内置用户文档参考：[内置用户](https://www.elastic.co/guide/en/elasticsearch/reference/current/built-in-users.html#set-built-in-user-passwords)
   >
   > elasticsearch.password 的密码为第二步执行命令设置的密码

   - 如果您不想将用户ID和密码放在`kibana.yml`文件中，请改为将其存储在密钥库中。运行以下命令以创建Kibana密钥库并添加安全设置[kibana-keystore命令参考](https://www.elastic.co/guide/en/kibana/7.8/secure-settings.html)

     ```shell
     bash-4.2$ ./bin/kibana-keystore create
     Created Kibana keystore in /usr/share/kibana/data/kibana.keystore
     bash-4.2$ ./bin/kibana-keystore add elasticsearch.username
     Enter value for elasticsearch.username: *************
     bash-4.2$ ./bin/kibana-keystore add elasticsearch.password
     Enter value for elasticsearch.password: **********
     
     ```

     

4. 访问kibana;

   ```
   http://192.168.10.191:5601
   ```

   > 使用超级账户登录，账户：elastic 密码：第二步执行命令设置的密码

5. 创建用户：导航到**Stack Management > Security > Users**

6. 创建角色：导航到**Stack Management > Security > Roles** [权限配置参数参考](https://www.elastic.co/guide/en/elasticsearch/reference/current/security-privileges.html#privileges-list-indices)

   > [参考](https://www.elastic.co/guide/en/elasticsearch/reference/current/security-getting-started.html)

## 安装遇到的问题

1. 提示最大虚拟内存太低

   ```
   max virtual memory areas vm.max_map_count [65530] is too low, increase to at least
   ```

   > 解决方案：执行命令： `sysctl -w vm.max_map_count=262144`



# fluentd

##  match、filter匹配规则

| 通配符               | 说明                                          | 示例                                                         |
| -------------------- | --------------------------------------------- | ------------------------------------------------------------ |
| `*`                  | 匹配单个标签部分                              | 例如，模式a。`*`匹配a.b，但不匹配a或a.b.c                    |
| `**`                 | 匹配零个或多个标签部分                        | 例如，模式a。`**`匹配a，a.b和a.b.c                           |
| `{X,Y,Z}`            | 匹配X，Y或Z，其中X，Y和Z是匹配模式            | 例如，模式{a，b}匹配a和b但不匹配c可以与`*`或`**`模式结合使用,例子，包括a.{b，c}.*和a.{b，c.**} |
| /regular expression/ | 用于复杂的匹配（fluentd v1.11.2起支持此功能） | 例如，模式/(?!a\.).*/匹配非a。开始的标签，例如b.xxx          |

>  注意： 当一个标签出现多个规则的时候 用一个或多个空格隔开

## match匹配顺序

fluentd匹配按标签在文档出现的顺序进行匹配。在编写配置文件定义的紧密匹配模式应放在较宽松的匹配模式之前，不然会被匹配，例如：

```ruby
# ** matches all tags. Bad :(
<match **>
  @type blackhole_plugin
</match>

<match myapp.access>
  @type file
  path /var/log/fluent/access
</match>
```

上面的 myapp.access将不会被匹配
正确的方式应该这样

```ruby
<match myapp.access>
  @type file
  path /var/log/fluent/access
</match>

# Capture all unmatched tags. Good :)
<match **>
  @type blackhole_plugin
</match>
```

如果你想将事件发送到多个输出，可以使用`copy`插件 例如：

```ruby
<match pattern>
  @type copy
  <store>
    @type file
    path /var/log/fluent/myapp1
    ...
  </store>
  <store>
    ...#存储一
  </store>
  <store>
    ...#存储二
  </store>
</match>
```

> 注意：
>
> 1、如果match出现两个相同的匹配规则，那么第二规则将不会被匹配。
>
> 2、 filter过滤器应放在match前，否则不会被触发

##  收集案例

### 客户端

```ruby
#
# /data/wwwroot/service/passport/logs/default
#服务请求日志收集
<source>
  @type tail
  path /data/wwwroot/service/passport/logs/default/%Y%m%d.log
  pos_file /tmp/td-agent/passport/passsport.pos.log 
  tag service.passport
 <parse service>
  # @type regexp
   @type none
  # expression /(\d{4}.*[^ ]) \| ([^ ]*) \| ([^ ]*) \| ([^ ]*) \| ([^ ]*) \| (?<result>[^ ]*)/
 </parse>
</source>

#调试
<filter service.*>
  @type    stdout
</filter>
    
<match *.*>
  @type forward
  send_timeout 2s
  recover_wait 10s 
  hard_timeout 60s 
  weight 60 #权重 如果一台服务器的权重为20，另一台服务器的权重为30，则事件以2：3的比率发送
<server>
   name service
    host 192.168.10.131
    port 24224
 </server>
#当所有服务器都不可用时使用的备份目标
  <secondary>
    @type secondary_file
    directory /tmp/td-agent/passport/send_error.log
  </secondary>
<buffer>
    flush_at_shutdown true #用于持久缓冲区 默认false
    flush_mode interval #指定时间刷新写入
    flush_interval 2s #两秒刷新写入
</buffer>
</match>

```

### 服务端

```ruby
##
<source>
  @type forward
  port 24224
  bind 0.0.0.0
</source>
# 调试
<filter service.passport>
    @type stdout
</filter>

<match *.**>
  @type copy
  <store>
    @type file
    path /data/td-agent/logs/${tag}/%Y-%m-%d
    symlink_path /data/td-agent/symlink
    append true
    #compress gzip
    <buffer tag,time>
       flush_at_shutdown true #用于持久缓冲区 默认false
       flush_mode interval #指定时间刷新写入
       flush_interval 2s #两秒刷新写入
    </buffer>
 </store>

 <store>
   @type elasticsearch
   hosts 192.168.10.131:9200 # 多个逗号隔开
   #host 192.168.10.131
   #port 9200
   #index_name nginx.log
   logstash_format true
   logstash_prefix ${tag}
  <buffer tag>
       flush_at_shutdown true #用于持久缓冲区 默认false
       flush_mode interval #指定时间刷新写入
       flush_interval 2s #两秒刷新写入
  </buffer>
 </store>
 <format>
    @type json
 </format>
</match>
```



# docker 

## docker logging  driver 日志转发

- docker 提供了一些机制允许我们从运行的容器中提取日志;这些机制统称为logging driver。对于docker  如果你没指定docker driver，其默认值为json-file 

  ```shell
  [root@localhost ~]# docker info |grep 'Logging Driver'
   Logging Driver: json-file
  
  ```

  > json-file 会将我们在控制台通过命令 `docker logs ` 看到的日志都保存在一个json文件中，我们可以在服务器的host上的容器目录中找到这个json文件

  ```shell
  [root@localhost ~]# docker info |grep 'Docker Root Dir'
   Docker Root Dir: /var/lib/docker
  ```

   > ```
   > 容器日志路径：/var/lib/docker/containers/<container-id>/<container-id>-json.log
   > ```

## docker 容器更改日志驱动器

[docker logging](https://docs.docker.com/compose/compose-file/#logging)
[fluentd驱动参考](https://docs.docker.com/config/containers/logging/fluentd/)

```yaml
  web:
    image: httpd
    container_name: httpd
    ports:
      - "80:80"
    links:
      - fluentd
    logging:
      driver: "fluentd"
      options:
        fluentd-address: localhost:24224
        tag: httpd.access
```



# es

## 检查健康

`````
GET /_cluster/health/
{
  "cluster_name" : "docker-cluster",
  "status" : "yellow",
  "timed_out" : false,
  "number_of_nodes" : 1,
  "number_of_data_nodes" : 1,
  "active_primary_shards" : 16,
  "active_shards" : 16,
  "relocating_shards" : 0,
  "initializing_shards" : 0,
  "unassigned_shards" : 8,
  "delayed_unassigned_shards" : 0,
  "number_of_pending_tasks" : 0,
  "number_of_in_flight_fetch" : 0,
  "task_max_waiting_in_queue_millis" : 0,
  "active_shards_percent_as_number" : 66.66666666666666
}

`````

> - cluster_name：（字符串）集群名称
> - status：（字符串）集群的运行状态；基于其主要和副本分片的状态
>   - green ：所有分片均已分别
>   - yellow：所有主分片均已分配，单未分配一个或多个副分片。如果集群中的某个节点发生故障，则在修复改节点之前，某些数据可能不可用
>   - red：未分配一个或多个主分片，因此某些数据不可用。在集群期间，这可能会短暂发生，因为已分配了主要分片
> - timed_out：（布尔值）如果false响应在timeout参数指定的时间段内返回（30s默认情况下）。
> - number_of_nodes：(整数)集群中的节点数
> - number_of_data_nodes：（整数）作为专用数据节点的节点数
> - active_primary_shards：（整数）活动主分区的数量
> - active_shards：（整数）活动主分区和副本分区的总数。
> - relocating_shards：（整数）正在重定位的分片的数量。
> - initializing_shards：（整数）正在初始化的分片数。
> - unassigned_shards：（整数）未分配的分片数。
> - delayed_unassigned_shards：（整数）其分配因超时设置而延迟的分片数。
> - number_of_pending_tasks：（整数）尚未执行的集群级别更改的数量。
> - number_of_in_flight_fetch：（整数）未完成的访存数量。
> - task_max_waiting_in_queue_millis：（整数）自最早的初始化任务等待执行以来的时间（以毫秒为单位）。
> - active_shards_percent_as_number：（浮动）群集中活动碎片的比率，以百分比表示

## 检查集群异常的索引

```
GET /_cat/indices\?v


health status index                          uuid                   pri rep docs.count docs.deleted store.size pri.store.size
yellow open   fluent.warn-20200811           c1UIGBUvQ9qt4FLHzvrx8w   1   1          7            0     23.7kb         23.7kb
yellow open   test.json-20200811             WWL5NIgpSJ2OIwF4b2GH7g   1   1          2            0     10.5kb         10.5kb
yellow open   service.passport-20200811      WOCv2RDESgeMPRJ8DMJdrQ   1   1          5            0      8.3kb          8.3kb
green  open   .apm-agent-configuration       REWQaAnRTtCpHepokYX95w   1   0          0            0       208b           208b
yellow open   moive_index                    lVxU_fI3SriMEqTVcVCZVg   1   1          3            0     10.8kb         10.8kb
green  open   .kibana_1                      qsbro1Z6S1mUsNLO2lYYUA   1   0        318            2      227kb          227kb
green  open   .security-7                    WCHBvuCrTfaAyEUcXURySw   1   0          7            0     23.8kb         23.8kb
yellow open   fluent.info-20200812           AFQ47wozQg26A4CZSKI50A   1   1          1            0      4.9kb          4.9kb
yellow open   httpd.access-20200811          pu4GkHHrTzK-WVt9Hyx5cw   1   1         21            0     18.5kb         18.5kb
green  open   .apm-custom-link               1EakyBweQyqy7z24iQuivg   1   0          0            0       208b           208b
green  open   .kibana_task_manager_1         paT439rfSTOVn3TICUMvSA   1   0          5            1     39.4kb         39.4kb
yellow open   fluent.info-20200811           XobWC94OQ4qtB62gXjzhHA   1   1         40            0       40kb           40kb
yellow open   fluent.info.20200811           gAtgm16yRzu-5dhOu3dOvw   1   1          2            0      9.7kb          9.7kb
green  open   kibana_sample_data_logs        iboWwh3URZuAZgia7JNVyQ   1   0      14074            0     11.2mb         11.2mb
green  open   .kibana-event-log-7.8.1-000001 RtZY9x7rS3yKqm2Y07joKw   1   0          2            0     10.4kb         10.4kb

```

- 查看es集群黄色状态索引的settings

  ```
  GET /fluent.warn-20200811/_settings
  
  {
    "fluent.warn-20200811" : {
      "settings" : {
        "index" : {
          "creation_date" : "1597131660567",
          "number_of_shards" : "1",
          "number_of_replicas" : "1",
          "uuid" : "c1UIGBUvQ9qt4FLHzvrx8w",
          "version" : {
            "created" : "7080199"
          },
          "provided_name" : "fluent.warn-20200811"
        }
      }
    }
  }
  ```

  


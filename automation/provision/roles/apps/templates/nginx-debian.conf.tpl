user  {{ nginx_user }};
worker_processes  {{ ansible_processor_vcpus }};

{% if nginx_geoip == true %}
    load_module modules/ngx_http_geoip_module.so;
{% endif %}

error_log  /var/log/nginx/error.log;
pid        /var/run/nginx.pid;

events {
    worker_connections  1024;
}

http {
    include       {{ nginx_path }}/mime.types;
    default_type  application/octet-stream;

    log_format  main  '$http_x_forwarded_for - $remote_user [$time_local] "$request" '
                      '$status $body_bytes_sent "$http_referer" '
                      '"$http_user_agent" $remote_addr';

    access_log  /var/log/nginx/access.log  main;

    client_max_body_size {{ nginx_client_max_body_size | default(8) }}m;
    server_tokens off;

    # Include performance settings
    include conf.d/performance.conf;

    # Activate the GZIP settings
    include conf.d/gzip.conf;

    map_hash_max_size 128;
    map_hash_bucket_size 900256;

    # Include the defined applications
    include {{ nginx_path }}/conf.d/sites-enabled/*;
}
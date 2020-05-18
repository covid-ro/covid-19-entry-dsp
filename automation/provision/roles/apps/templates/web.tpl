server {
    server_name {{item.value.servername}};
    listen  80;

    return 301 https://$host$request_uri;
}

server {
    server_name {{item.value.servername}};
    listen  443;

    # Setup the project root
    root {{item.value.public}};

    # Setup error logging
    error_log /var/log/nginx/{{item.key}}_error.log;
    access_log /var/log/nginx/{{item.key}}_access.log main;

    # Setup SSL
    ssl on;
    ssl_certificate /etc/ssl/{{item.value.certificate}}/fullchain.pem;
    ssl_certificate_key /etc/ssl/{{item.value.certificate}}/privkey.pem;

    ssl_protocols TLSv1.1 TLSv1.2;
    ssl_ciphers "ECDHE-RSA-AES256-GCM-SHA384:ECDHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-SHA384:ECDHE-RSA-AES128-SHA256:ECDHE-RSA-AES256-SHA:ECDHE-RSA-AES128-SHA:DHE-RSA-AES256-SHA256:DHE-RSA-AES128-SHA256:DHE-RSA-AES256-SHA:DHE-RSA-AES128-SHA:ECDHE-RSA-DES-CBC3-SHA:EDH-RSA-DES-CBC3-SHA:AES256-GCM-SHA384:AES128-GCM-SHA256:AES256-SHA256:AES128-SHA256:AES256-SHA:AES128-SHA:DES-CBC3-SHA:HIGH:!aNULL:!eNULL:!EXPORT:!DES:!MD5:!PSK:!RC4";
    ssl_prefer_server_ciphers on;
    ssl_session_timeout 1d;
    ssl_session_cache shared:SSL:50m;
    ssl_stapling on;
    ssl_stapling_verify on;

{% if item.value.dhparam is defined %}
    ssl_dhparam {{ item.value.dhparam_path }};
{% endif %}

{% if item.value.http_auth is defined %}
    # Include basic authentication configuration
    include conf.d/httpauth.conf;
{% endif %}

	# Include security settings
    include conf.d/security.conf;

    # Include static content settings
    # include conf.d/static-content.conf;

    # Try to serve files directly, redirect to backend if we cannot find them
    location / {
        # try to serve file directly, fallback to index.php
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php{{ php_version }}-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;

        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;

{% if item.value.env is defined %}
        fastcgi_param APP_ENV {{item.value.env}};
{% else %}
        fastcgi_param APP_ENV 'prod';
{% endif %}

        internal;
    }

# symfony nginx config example/recommandation here:
# https://symfony.com/doc/2.8/setup/web_server_configuration.html#nginx

    location ~ \.php$ {
        return 404;
    }

}

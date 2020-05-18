satisfy any;

# Allow local connections.
allow 127.0.0.1/32;

# Allow ips explicitly defined.
{% for ip in item.value.http_auth_allowed_ips|default([]) %}
allow {{ ip }};
{% endfor %}

deny all;

auth_basic           "Restricted web site";
auth_basic_user_file conf.d/passwdfile;

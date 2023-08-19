FROM nginx

RUN rm /etc/nginx/conf.d/default.conf
COPY ./cert/cert.crt /etc/ssl
COPY ./cert/key.key /etc/ssl
COPY nginx.conf /etc/nginx/conf.d

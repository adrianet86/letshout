FROM nginx:alpine

COPY /src/default.conf /etc/nginx/conf.d/default.conf

#COPY fullchain.pem /etc/nginx/fullchain.pem
#COPY privkey.pem /etc/nginx/privkey.pem

RUN mkdir /app && touch /app/index.php
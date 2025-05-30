FROM nginx:1.10.1-alpine

# Kopiere komplettes vinotec-Verzeichnis in den HTML-Ordner
COPY vinotec/ /usr/share/nginx/html/

EXPOSE 8080

CMD ["nginx", "-g", "daemon off;"]

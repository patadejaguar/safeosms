# SAFE-OSMS Docker



Dockerfile content :

```dockerfile
##		Init Docker File
FROM tutum/lamp:latest

##		Author
MAINTAINER Luis Balam .- patadejaguar@gmail.com

##		Metadata

LABEL version="0.02"

LABEL description="SAFE-OSMS Microfinance Suite"

RUN rm -fr /app && git clone https://github.com/patadejaguar/safeosms.git /app && git clone https://github.com/patadejaguar/safeosmsdb.git /app/install/db && chown -R www-data:www-data /app

RUN apt-get update && apt-get upgrade --yes

RUN apt-get install wget curl memcached apache2 mysql-server libapache2-mod-auth-mysql php5-mysql php5 libapache2-mod-php5 php5-mcrypt php5-xsl php5-gd php5-curl php5-memcache php-crypt-blowfish  --yes


RUN wget --no-check-certificate -O code.install.sh https://www.dropbox.com/s/tw8maoavfsaakbe/code.install.sh?dl=1
RUN wget --no-check-certificate -O sql.backup.sh https://www.dropbox.com/s/ucrw9axkqyd43oo/sql.backup.sh?dl=1
RUN wget --no-check-certificate -O sql.update.sh https://www.dropbox.com/s/rf96d32z98js6hr/sql.update.sh?dl=1
RUN wget --no-check-certificate -O uninstall.sh https://www.dropbox.com/s/natzbranlz96499/uninstall.sh?dl=1
RUN wget --no-check-certificate -O sql.restore.sh https://www.dropbox.com/s/b85lvlitls78rxn/sql.restore.sh?dl=1
RUN chmod +x *.sh
RUN ./code.install.sh ./app www-data

RUN rm htdocs.tar.gz


EXPOSE 80 3306
CMD ["/run.sh"]


##		End Docker File

```

Build Docker:

```bash
sudo docker build ./ -t sipakal/safeosms
```

Run Docker :

```bash
sudo docker run -d -p 8085:80 -p 3307:3306 sipakal/safeosms
```


Show Docker images :

```bash
sudo docker ps
```

Enter to Docker Image :

```bash
sudo docker exec -it [CONTAINER_ID] bash
```

Clean all Docker images :



```bash
sudo docker kill $(sudo docker ps -q)
sudo docker rmi -f $(sudo docker images -a -q)
#sudo docker system prune -a

```






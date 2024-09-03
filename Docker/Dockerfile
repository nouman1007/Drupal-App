FROM amazonlinux:2
RUN yum -y install openssh-server httpd
RUN echo "root:Docker!" | chpasswd
RUN ssh-keygen -A
COPY ./sshd_config /etc/ssh/.
EXPOSE 2222 80
COPY ./start.sh start.sh
RUN chmod +x ./start.sh
CMD ./start.sh

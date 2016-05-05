#! /bin/sh
. $HOME/.bash_profile

time=`date "+%w"`
dmp_file="zhrd-$time.dmp"
#log_file="dmp_exp-$time.log"
bak="zhrd-attach-$time.tar.z"

FRONTPASS=zhrdftp
FRONTUSER=zhrdftp

cd /appdata/zhrd/

exp zhrd/zhrd file=${dmp_file} owner=zhrd

tar cvzf $bak *
echo $bak created successfull!!

ftp -i -n 192.168.101.57 << !
user $FRONTUSER $FRONTPASS
bin
prom off
put $bak
put $dmp_file
by
!

echo ftp put successfull!!

rm -f $bak
rm -f $dmp_file
echo delete successfull!!

#! /bin/sh
. $HOME/.bash_profile

time=`date "+%w"`
dmp_file="zhrd-$time.dmp"
log_file="dmp_imp-$time.log"
bak="zhrd-attach-$time.tar.z"

cd /appdata/zhrdftp/
mv $bak /appdata/zhrd/
mv $dmp_file /appdata/zhrd/

cd /appdata/zhrd/
tar -xvzf $bak

imp zhrd/zhrd file=${dmp_file} log=${log_file} ignore=y

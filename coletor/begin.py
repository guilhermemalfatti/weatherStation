#!/usr/bin/python

import os
import time

try :
   os.system('killall gammu-smsd')
except :
   pass


try :
   os.system('gammu-smsd -c /etc/gammu-smsdrc &')
except :
   pass
   # aqui ligar um led por exemplo se nao conseguir iniciar o servico




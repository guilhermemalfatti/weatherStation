#!/usr/bin/env python
# -*- coding:utf-8 -*-

import sys
import gammu
import MySQLdb


# Open database connection
db = MySQLdb.connect("localhost","root","123456","smsd" )

# prepare a cursor object using cursor() method
cursor = db.cursor()

# disconnect from server
print "fazendo select..."
sql = "SELECT substring(date,1, 13) identificacao, date, avg(temp), avg(umidade), flag FROM dados where flag = 0 group by substring(date,1, 13);"

try:
   # Execute the SQL command
   cursor.execute(sql)
   # Fetch all the rows in a list of lists.
   results = cursor.fetchall()
   for row in results:
	  sm = gammu.StateMachine()
	  sm.ReadConfig()
	  sm.Init()#
	  t = str(row[2])+"000000000000"
	  h = str(row[3])+"000000000000"	  
          
	  message = {
                  'Text': "352 "+str(row[1])+" "+t[0:14]+" "+h[0:14],
		  'SMSC': {'Location': 1},
		 'Number': '0145484100654'
	  }
	  print "enviando sms..."

	  sm.SendSMS(message)
	  print"Sms enviado"
	  
      sql = "update dados set  flag = 1 where substring(date, 1, 13) = '" + str(row[0]) + "'"	  
	  print "fazendo update...-> "+ sql
      cursor.execute(sql)
      db.commit()
          
except e:
   print "Error: unable to fecth data "+ e
   db.close()

db.close()





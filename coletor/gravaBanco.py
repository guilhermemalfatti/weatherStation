#!/usr/bin/env python
# -*- coding:utf-8 -*-
#arquivo chamado a cada 15 mim para para gravar dados no BD
import sys
import dhtreader
import gammu
import MySQLdb

DHT11 = 11
DHT22 = 22
AM2302 = 22


dhtreader.init()

if len(sys.argv) != 3:
    print("usage: {0} [11|22|2302] GPIOpin#".format(sys.argv[0]))
    print("example: {0} 2302 Read from an AM2302 connected to GPIO #4".format(sys.argv[0]))
    sys.exit(2)

dev_type = None
if sys.argv[1] == "11":
    dev_type = DHT11
elif sys.argv[1] == "22":
    dev_type = DHT22
elif sys.argv[1] == "2302":
    dev_type = AM2302
else:
    print("invalid type, only 11, 22 and 2302 are supported for now!")
    sys.exit(3)

dhtpin = int(sys.argv[2])
if dhtpin <= 0:
    print("invalid GPIO pin#")
    sys.exit(3)

print("using pin #{0}".format(dhtpin))
t, h = dhtreader.read(dev_type, dhtpin)
if t and h:
    print("Temp = {0} *C, Hum = {1} %".format(t, h))
else:
    print("Failed to read from sensor, maybe try again?")



# Open database connection
db = MySQLdb.connect("localhost","root","123456","smsd" )

# prepare a cursor object using cursor() method
cursor = db.cursor()

# Prepare SQL query to INSERT a record into the database.
sql = "insert into dados values(null, now(), "+str(t)+", "+str(h)+", 0);"

try:
   # Execute the SQL command
    cursor.execute(sql)
    # Commit your changes in the database
    db.commit()
except:
    # Rollback in case there is any error
    db.rollback()
    db.close()
    print "Error02"


# disconnect from server

db.close()


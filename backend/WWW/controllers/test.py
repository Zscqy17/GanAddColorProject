#!D:\Miniconda3\python.exe
import sys
import os

from click import option
def addColor():
    os.environ['HOMEPATH']="D:/Miniconda3"
    paths=sys.argv[1]
    option=sys.argv[2]
    if(option=="1"):
        os.system('D:/Miniconda3/python.exe ./controllers/test2.py %s' % (paths))
    if(option=="2"):
        os.system('D:/Miniconda3/python.exe ./controllers/test3.py %s' % (paths))
    print(sys.argv[2])
if __name__=='__main__':
    addColor()
#-*-coding:utf-8-*- #编码声明，不要忘记！
import time
import urllib.request  
import requests
import re
from lxml import html   
from bs4 import BeautifulSoup 


p = re.compile('/book/.....')  
  
url11 = "https://www.zwdu.com"  #网站链接
url = "https://www.zwdu.com/quanben/"   #全本小说列表链接
  
opener = urllib.request.build_opener()  
opener.addheaders = [('User-agent', 'Mozilla/5.0')]  
  
html = opener.open(url).read().decode('gbk')  #这个网站页面是gbk格式的

allfinds = p.findall(html)  
  
#mypages = list(set(allfinds))  
mypages = list(allfinds)

#mypages = enumerate(mypages)#去除重复项
#print(mypages)

for i , art in enumerate(mypages) :#循环小说目录
	try :  
		print(url11,art)
		detail = opener.open(url11+art).read().decode('gbk')  
		#print(detail)
		title = re.search('<h1>(.*?)</h1>',detail)#小说名称
		author = re.search('<p>作&nbsp;&nbsp;&nbsp;&nbsp;者：(.*?)</p>',detail)#小说作者
		contont = re.findall('<dd><a href="(.*?)">',detail)#章节链接列表
		#zj = list(set(contont))
		zj = list(contont)
		print(title.group(1))
		print(author.group(1))
		#print(zj)
		for index , page in enumerate(zj) :#循环章节目录
			try :  
				pageContent = opener.open(url11+page).read().decode('gbk')  
				zjtitle = re.search('<h1>(.*?)</h1>',pageContent)#章节标题
				zjText = re.search('<div id="content">(.*?)</div>',pageContent)#章节内容
				dr = re.compile(r'<[^>]+>',re.S)
				dd = dr.sub('',zjText.group(1))
				dd = dd.replace('&nbsp;', '')#去掉内容中的html代码
				print(zjtitle.group(1))#输出章节标题
				#print(dd)
				fname = title.group(1)+'.txt';
				fobj=open(fname,'a+')#如果已存在小说的txt文件，就在文本末尾写入，不存在就新建一个
				fobj.write('\n'+zjtitle.group(1))#写入章节标题
				fobj.write('\n'+dd)#写入章节内容
				fobj.close()
			except urllib.error.HTTPError:  
				print('urllib.error.HTTPError')  
				time.sleep(20) #如果报错就休息20s
			except urllib.error.URLError:  
				print('urllib.error.URLError')  
				time.sleep(20)  #如果报错就休息20s
			time.sleep(0.5) 
	except urllib.error.HTTPError:  
		print('urllib.error.HTTPError')  
		time.sleep(20)
              
	except urllib.error.URLError:  
		print('urllib.error.URLError')  
		time.sleep(20)  
	time.sleep(0.5) 




  


		

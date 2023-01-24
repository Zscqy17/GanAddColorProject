import re
str="[{url:http://498f26878q.qicp.vip/imgs/6258e9fdb3e38.jpg},{url:http://498f26878q.qicp.vip/imgs/6258ea4baf6e3.png}]"

results = re.findall(r"(imgs/{0,}/(\w){0,}\.(jpg|png))",str)
# 没有创建正则表达式对象时，也可以用这种方法
# results = re.findall(r'\d+', content)
for result in results:
    print(result[0])
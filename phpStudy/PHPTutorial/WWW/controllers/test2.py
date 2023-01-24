import os
import sys
import torch
import torch.nn as nn
import torch.nn.functional as F
from torchvision import transforms
import matplotlib.pyplot as plt
from PIL import Image
import re


paths=[]
os.environ['HOMEPATH']="D:/Miniconda3"
data=sys.argv[1]
results = re.findall(r"(imgs/{0,}/(\w){0,}\.(jpg|png))",data)


for result in results:
    paths.append("E:/phpStudy/PHPTutorial/WWW/"+result[0])
    
class Downsample(nn.Module):
    def __init__(self, in_channels, out_channels):
        super(Downsample, self).__init__()
        self.conv_relu = nn.Sequential(
                            nn.Conv2d(in_channels, out_channels, 
                                      kernel_size=3, stride=2, padding=1),
                            nn.LeakyReLU(0.2,inplace=True),
            )
        self.bn = nn.BatchNorm2d(out_channels)
    def forward(self, x, is_bn=True):
        x = self.conv_relu(x)
        if is_bn:
            x = self.bn(x)
        return x

class Upsample(nn.Module):
    def __init__(self, in_channels, out_channels):
        super(Upsample, self).__init__()
        self.upconv_relu = nn.Sequential(
                               nn.ConvTranspose2d(in_channels, out_channels, 
                                                  kernel_size=3,
                                                  stride=2,
                                                  padding=1,
                                                  output_padding=1),
                               nn.ReLU()
            )
        self.bn = nn.BatchNorm2d(out_channels)
        
    def forward(self, x, is_drop=False):
        x = self.upconv_relu(x)
        x = self.bn(x)
        if is_drop:
            x = F.dropout2d(x)
        return x

class Generator(nn.Module):
    def __init__(self):
        super(Generator, self).__init__()
        self.down1 = Downsample(3, 64)
        self.down2 = Downsample(64, 128)
        self.down3 = Downsample(128, 256)
        self.down4 = Downsample(256, 512)
        self.down5 = Downsample(512, 512)
        self.down6 = Downsample(512, 512)
        self.down7 = Downsample(512, 512)
        self.down8 = Downsample(512, 512)
        
        self.up1 = Upsample(512, 512)
        self.up2 = Upsample(1024, 512)
        self.up3 = Upsample(1024, 512)
        self.up4 = Upsample(1024, 512)
        self.up5 = Upsample(1024, 256)
        self.up6 = Upsample(512, 128)
        self.up7 = Upsample(256, 64)
        self.up8 = Upsample(128, 64)

        self.last = nn.Conv2d(64,3,3,1,1)
        

    def forward(self, x):
        x1 = self.down1(x, is_bn=False)   # torch.Size([8, 64, 128, 128])
        x2 = self.down2(x1)               # torch.Size([8, 128, 64, 64])
        x3 = self.down3(x2)               # torch.Size([8, 256, 32, 32])
        x4 = self.down4(x3)               # torch.Size([8, 512, 16, 16])
        x5 = self.down5(x4)               # torch.Size([8, 512, 8, 8])
        x6 = self.down6(x5)               # torch.Size([8, 512, 4, 4])
        x7 = self.down7(x6)               # torch.Size([8, 512, 2, 2])
        x8 = self.down8(x7)               # torch.Size([8, 512, 1, 1])
        
        x8 = self.up1(x8, is_drop=True)   # torch.Size([8, 512, 2, 2])
        x8 = torch.cat([x7, x8], dim=1)   # torch.Size([8, 1024, 2, 2])

        x8 = self.up2(x8, is_drop=True)   # torch.Size([8, 512, 4, 4])
        x8 = torch.cat([x6, x8], dim=1)   # torch.Size([8, 1024, 2, 2])
        
        x8 = self.up3(x8, is_drop=True)   # torch.Size([8, 512, 8, 8])
        x8 = torch.cat([x5, x8], dim=1)   # torch.Size([8, 1024, 8, 8])
        
        x8 = self.up4(x8)                 # torch.Size([8, 512, 16, 16])
        x8 = torch.cat([x4, x8], dim=1)   # torch.Size([8, 1024, 16, 16])
        
        x8 = self.up5(x8)                         
        x8 = torch.cat([x3, x8], dim=1)          
        
        x8 = self.up6(x8)                       
        x8 = torch.cat([x2, x8], dim=1)         
        
        x8 = self.up7(x8)                        
        x8 = torch.cat([x1, x8], dim=1)         
        
        x8 = self.up8(x8)
        x8 = torch.tanh(self.last(x8))           
        return x8

gen_pe = Generator()

gen_pe = torch.load("E:\\OneDrive\\桌面\\毕业设计\\模型\训练模型\\训练模型论文，细节增强\\gen.plk")

def show_img(pre_img,test_img,true_img,img_path):
    prediction = pre_img.permute(0, 2, 3, 1).detach().cpu().numpy()
    test_img= test_img.permute(0, 2, 3, 1).cpu().numpy()
    true_img= true_img.permute(0, 2, 3, 1).cpu().numpy()
    plt.figure(figsize=(15, 15))
    display_list = [test_img[0], true_img[0], prediction[0]]
    title = ['Input Image', 'Ground Truth', 'Predicted Image']
    for i in range(3):
        plt.subplot(1, 3, i+1)
        plt.title(title[i])
        plt.imshow(display_list[i] * 0.5 + 0.5)
        plt.axis('off')
    plt.savefig(img_path)
    plt.show()
    

def img_save(model,img,img_path):
    prediction = model(img).permute(0, 2, 3, 1).detach().cpu().numpy()
    prediction_img=prediction[0]
    plt.subplot(1, 1, 1)
    plt.imshow(prediction_img* 0.5 + 0.5)
    plt.axis('off')
    plt.savefig(img_path, dpi=300,bbox_inches='tight', transparent="True", pad_inches=0)
    

transform = transforms.Compose([
    transforms.ToTensor(),                        # 取值范围会被归一化到(0, 1)之间
    transforms.Normalize(mean=0.5, std=0.5)       # 设置均值和方差均为0.5
])

for path in paths:
    img = Image.open(path).convert('RGB')

    # img_c = Image.open('C:/Users/Administrator/Desktop/上色test/有色.png')
    img = img.resize((512, 512), Image.BICUBIC)
    img = transform(img)
    #img_c= transform(img_c)

    img = img.unsqueeze(0).to('cuda')
    #img_c = img_c.unsqueeze(0).to('cuda')

    newimg = gen_pe(img)

    #show_img(newimg,img,img_c)
    img_save(gen_pe,img,path)


print('ok')

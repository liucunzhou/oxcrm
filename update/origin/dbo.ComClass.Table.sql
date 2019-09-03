USE [Wedding]
GO
/****** Object:  Table [dbo].[ComClass]    Script Date: 2019/7/24 16:01:31 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[ComClass](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](500) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](50) NULL,
	[field_6] [varchar](500) NULL,
	[field_7] [text] NULL,
	[field_8] [varchar](500) NULL,
	[field_9] [varchar](200) NULL,
	[field_10] [text] NULL,
	[field_11] [varchar](500) NULL,
	[field_12] [varchar](500) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_ComClass] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[ComClass] ON 

INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4, N'附件应用', 0, N'||', N'comclass_a147694cb58347de80d9e1565220a6d3', N'0', N'', N'', N'', N'ShangChuanZiYuan', N'', N'', N'', CAST(0x0000A08100000000 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (5, N'视频文件类', 0, NULL, N'comclass_ad28c9bbd5fd4a38992ce64fd3caccf7', N'comclass_a147694cb58347de80d9e1565220a6d3', N'88', N'', NULL, NULL, NULL, NULL, NULL, CAST(0x0000A08100000000 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (6, N'动画文件类', 0, NULL, N'comclass_ffbd8679f3594414a6ee8b0b7eee1ec9', N'comclass_a147694cb58347de80d9e1565220a6d3', N'88', N'', NULL, NULL, NULL, NULL, NULL, CAST(0x0000A08100000000 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (7, N'音频文件类', 0, NULL, N'comclass_84c29d5778084e129a18ef603f015737', N'comclass_a147694cb58347de80d9e1565220a6d3', N'88', N'', NULL, NULL, NULL, NULL, NULL, CAST(0x0000A08100000000 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (8, N'图片文件类', 0, N'||', N'comclass_3e89ec957c534e5fbafb82bfc91a2fad', N'comclass_a147694cb58347de80d9e1565220a6d3', N'|4|', N'', N'', N'', N'', N'', N'', CAST(0x0000A08100000000 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (9, N'排版图片', 0, NULL, N'comclass_a74807ff5c2743559f1ab2ae9abeedfe', N'comclass_3e89ec957c534e5fbafb82bfc91a2fad', N'', N'', NULL, NULL, NULL, NULL, NULL, CAST(0x0000A08100000000 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (10, N'文字图片', 0, NULL, N'comclass_6b8361055ed1482da28d9a0bb7d959dd', N'comclass_3e89ec957c534e5fbafb82bfc91a2fad', N'', N'', NULL, NULL, NULL, NULL, NULL, CAST(0x0000A08100000000 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (11, N'表情图片', 0, NULL, N'comclass_c1190b12f0804679b9535e4c42ed8037', N'comclass_3e89ec957c534e5fbafb82bfc91a2fad', N'', N'', NULL, NULL, NULL, NULL, NULL, CAST(0x0000A08100000000 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (22, N'列表小图', 0, N'||', N'ComClass_2012914112211', N'comclass_5413b89420f24ddd95cceb972f197b22', N'|comclass_a147694cb58347de80d9e1565220a6d3||comclass_5413b89420f24ddd95cceb972f197b22|', N'', N'', N'LieBiaoXiaoTu', N'', N'', N'', CAST(0x0000A0CC00BB9C20 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (23, N'软件皮肤文件', 0, N'', N'ComClass_201291511351', N'comclass_a147694cb58347de80d9e1565220a6d3', N'|4|', N'', N'', NULL, NULL, NULL, NULL, CAST(0x0000A0CD00BF1A44 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (24, N'高级皮肤', 0, N'||', N'ComClass_201291513843', N'ComClass_201291511351', N'|4||23|', N'', N'', N'', N'', N'', N'', CAST(0x0000A0CD00D8BCC4 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (25, N'系统桌布', 0, N'||', N'ComClass_20129151399', N'comclass_a147694cb58347de80d9e1565220a6d3', N'|4|', N'', N'', N'', N'', NULL, NULL, CAST(0x0000A0CD00D8CC00 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (51, N'证书文件类', 0, N'||', N'ComClass_201211693642', N'comclass_a147694cb58347de80d9e1565220a6d3', N'|4|', N'', N'', N'', N'', N'', N'', CAST(0x0000A101009E7A50 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (87, N'新闻图片', 0, N'||', N'comclass_4a24dbb3fa274a02b498e3e14351da09', N'comclass_a147694cb58347de80d9e1565220a6d3', N'|comclass_a147694cb58347de80d9e1565220a6d3|', N'', N'', N'XinWenTuPian', N'', N'', N'', CAST(0x0000A33F00DF2690 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (88, N'栏目图片', 0, N'||', N'comclass_5413b89420f24ddd95cceb972f197b22', N'comclass_a147694cb58347de80d9e1565220a6d3', N'|comclass_a147694cb58347de80d9e1565220a6d3|', N'', N'', N'LanMuTuPian', N'', N'|5||4||2||1|', N'', CAST(0x0000A342013B48E4 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (109, N'广告图片', 0, N'||', N'comclass_45dd5b2ab6a64bf793b7f352aa585d74', N'comclass_a147694cb58347de80d9e1565220a6d3', N'|4|', N'', N'', N'GuangGaoTuPian', N'', N'', N'', CAST(0x0000A357001BBEBC AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (115, N'详情大图', 0, N'||', N'comclass_08b85f3b48464bb79b69f4428f54d66a', N'comclass_5413b89420f24ddd95cceb972f197b22', N'|comclass_a147694cb58347de80d9e1565220a6d3||comclass_5413b89420f24ddd95cceb972f197b22|', N'', N'', N'XiangQingDaTu', N'', N'', N'', CAST(0x0000A358011ADB54 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (133, N'轮换图组', 0, N'', N'comclass_82b676d07ef1496880d5737cad518568', N'comclass_5413b89420f24ddd95cceb972f197b22', N'|comclass_a147694cb58347de80d9e1565220a6d3||comclass_5413b89420f24ddd95cceb972f197b22|', N'', N'', N'LunHuanTuZu', N'', N'', N'', CAST(0x0000A3A600E96B14 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (266, N'下载附件', 0, N'/UPath/2015/1/6/94930511755040ufi.png', N'comclass_b40866d24c2f4c1ea1d269ec39e6665e', N'comclass_a147694cb58347de80d9e1565220a6d3', N'|comclass_a147694cb58347de80d9e1565220a6d3|', N'店铺上传', N'', N'XiaZaiFuJian', N'店铺上传', N'', N'', CAST(0x0000A41800A1F61C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (296, N'广告分类', 0, N'', N'comclass_0a831f2a43b543fab6a5beb713a70d58', N'0', N'|End0|', N'广告', N'', N'GuangGaoFenLei', N'广告', N'', N'', CAST(0x0000A43100BB5CD8 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (328, N'用户证件分类', 0, N'', N'comclass_643ccae40014414da328705032ac87a4', N'comclass_a147694cb58347de80d9e1565220a6d3', N'|comclass_a147694cb58347de80d9e1565220a6d3|', N'用户图片分类', N'', N'YongHuZhengJianFenLei', N'用户图片分类', N'', N'', CAST(0x0000A496010DEDF4 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (329, N'身份证正面照', 0, N'', N'comclass_c2b96e382129485d91575cbab5bd2324', N'comclass_643ccae40014414da328705032ac87a4', N'|comclass_a147694cb58347de80d9e1565220a6d3||comclass_643ccae40014414da328705032ac87a4|', N'身份证正面照', N'', N'PIDCard', N'身份证正面照', N'', N'', CAST(0x0000A496010E1E00 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (330, N'身份证反面照', 0, N'', N'comclass_937bbf3814b846f3963d85f35133518c', N'comclass_643ccae40014414da328705032ac87a4', N'|comclass_a147694cb58347de80d9e1565220a6d3||comclass_643ccae40014414da328705032ac87a4|', N'身份证反面照', N'', N'CIDCard', N'身份证反面照', N'', N'', CAST(0x0000A496010E30C0 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (331, N'职业资格证', 0, N'', N'comclass_3c1a96a7d798449bbe0ab6e01dfa054e', N'comclass_643ccae40014414da328705032ac87a4', N'|comclass_a147694cb58347de80d9e1565220a6d3||comclass_643ccae40014414da328705032ac87a4|', N'职业资格证', N'', N'QPhoto', N'职业资格证', N'', N'', CAST(0x0000A496010E4830 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (355, N'消息分类', 0, N'', N'comclass_cbd82f091cd943688e670d844c7847e6', N'0', N'|End0|', N'消息分类', N'', N'XiaoXiFenLei', N'消息分类', N'', N'', CAST(0x0000A4F400E7A590 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (358, N'提示信息', 16, N'/UPath/2015/10/9/19425193216313ufi.png', N'comclass_f116da8065a6401b85efa230b111bdaf', N'comclass_cbd82f091cd943688e670d844c7847e6', N'|End0||comclass_cbd82f091cd943688e670d844c7847e6|', N'提示信息', N'', N'TiShiXinXi', N'提示信息', N'', N'', CAST(0x0000A4F400E7D59C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (366, N'品牌', 0, N'', N'comclass_25bcf4d306284061836e7a5a874cb4ab', N'0', N'', N'品牌', N'', N'PinPai', N'品牌', N'', N'', CAST(0x0000A4F400EBBE64 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (367, N'天天果园', 0, N'', N'comclass_0a30a1ff747d431894e7c40b3e2dbe46', N'comclass_25bcf4d306284061836e7a5a874cb4ab', N'|comclass_25bcf4d306284061836e7a5a874cb4ab|', N'天天果园', N'', N'TianTianGuoYuan', N'天天果园', N'', N'', CAST(0x0000A4F400EBCDA0 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (368, N'评论分类', 0, N'', N'comclass_4fc924567e6b4ec78badc3ee06638560', N'0', N'', N'评论分类', N'', N'PingLunFenLei', N'评论分类', N'', N'', CAST(0x0000A50100EDCB64 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (369, N'在线吐槽', 0, N'', N'comclass_82c66f441e354ca8bf39a91507925f28', N'comclass_4fc924567e6b4ec78badc3ee06638560', N'|comclass_4fc924567e6b4ec78badc3ee06638560|', N'在线吐槽', N'', N'ZaiXianTuCao', N'在线吐槽', N'', N'', CAST(0x0000A50100EDD974 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (370, N'评论商品', 0, N'', N'comclass_23c9c4b0a03845489e90cb0151a9f3b9', N'comclass_4fc924567e6b4ec78badc3ee06638560', N'|comclass_4fc924567e6b4ec78badc3ee06638560|', N'评论商品', N'', N'PingLunShangPin', N'评论商品', N'', N'', CAST(0x0000A50100EDEC34 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (373, N'微信欢迎广告', 0, N'', N'comclass_a1e8be26925e446781dd051649372602', N'comclass_0a831f2a43b543fab6a5beb713a70d58', N'|End0||comclass_0a831f2a43b543fab6a5beb713a70d58|', N'首页导航', N'', N'WeiXinHuanYingGuangGao', N'首页导航', N'', N'', CAST(0x0000A53001056C9C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (374, N'服务内容', 0, N'', N'comclass_29fe8ed86ce645d1ba384114c881cbe9', N'comclass_0a831f2a43b543fab6a5beb713a70d58', N'|End0||comclass_0a831f2a43b543fab6a5beb713a70d58|', N'首页推广', N'', N'FuWuNaRong', N'首页推广', N'', N'', CAST(0x0000A53001057AAC AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (375, N'资讯类别', 0, N'', N'comclass_f7085d7f0b404968bf17d24d0e483b6b', N'0', N'', N'社区服务', N'', N'ZiXunLeiBie', N'社区服务', N'', N'', CAST(0x0000A55A00F27204 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (376, N'行业新闻', 0, N'', N'comclass_7c958411c0d94dafbd4921d653162210', N'comclass_f7085d7f0b404968bf17d24d0e483b6b', N'|End0||comclass_f7085d7f0b404968bf17d24d0e483b6b|', N'12320热线', N'', N'XingYeXinWen', N'12320热线', N'', N'', CAST(0x0000A55A00F28398 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (377, N'关键标签', 0, N'', N'comclass_a39bbe04a69441e693ed30683e33c0c3', N'0', N'', N'', N'', N'GuanJianBiaoQian', N'', N'', N'', CAST(0x0000A5B00173F388 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (378, N'文案范本', 0, N'', N'comclass_59997e3af4924bc2b255db8be75ddb49', N'comclass_a39bbe04a69441e693ed30683e33c0c3', N'|End0||comclass_a39bbe04a69441e693ed30683e33c0c3|', N'', N'', N'WenAnFanBen', N'', N'', N'', CAST(0x0000A5B001741A34 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (379, N'修身养性', 0, N'', N'comclass_6bdf581a25354500bbcaa1b716cdd74f', N'comclass_a39bbe04a69441e693ed30683e33c0c3', N'|End0||comclass_a39bbe04a69441e693ed30683e33c0c3|', N'', N'', N'XiuShenYangXing', N'', N'', N'', CAST(0x0000A5B001742F4C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (380, N'业务知识', 0, N'', N'comclass_c1d1863e5d1043d58631e004879b2d55', N'comclass_a39bbe04a69441e693ed30683e33c0c3', N'|End0||comclass_a39bbe04a69441e693ed30683e33c0c3|', N'', N'', N'YeWuZhiShi', N'', N'', N'', CAST(0x0000A5B001744914 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (381, N'程序接口', 0, N'', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'0', N'', N'', N'', N'ChengXuJieKou', N'', N'', N'', CAST(0x0000A62500AE7680 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (382, N'【005】添加反馈（PostCGMessage）', 35, N'', N'comclass_81a9195e096b4cdcaa2b304eaad6670a', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'=========GET=============================================
必选项--cmd=adit--（添加）
=========POST============================================
必选项--userid=clienter_d5dd8050da094309a9377bb8fb07f8f8--（网名或用户SID）
必选项--cate=0--（类型）意见返馈|0*人员评论|1*疑惑评论|2*服务评论|3
必选项--message=你好--（信息主体）
选填项--jusersid=clienter_d5dd8050da094309a9377bb8fb07f8f8--（接收用户的sid）
选填项--appid=clienter_60a698eb27bb443da751dad881ef1942--（留言对像SID）
选填项--contact=18270164890--（联系方式）
选填项--statu=0--（0禁止，1通过，2采纳）
=========Model===========================================
<?xml version="1.0"?>
<root title="根据经纬度取城市">
  <result msg="上海市" code="200" time="136.6906毫秒">
    <inf sid="lwovPX6nGTq%2fOfd" name="上海市" />
  </result>
</root>





==================== POST ==================

==================== 用例 ===================
=================== RETURN =================
<?xml version="1.0"?>
<root title="客户反馈">
<result msg="新增数据成功" code="200" />
</root>
============================================', N'/CAPool/PostCGMessage.aspx', N'ZongPeiZhiWenJian', N'', N'', N'', CAST(0x0000A62500AF0578 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (384, N'ZCM使用习惯上报（PostCBehavior）', 28, N'', N'comclass_fad23e02a77a4b8fb690a983e0a77536', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'提交用户使用习惯：
==================== POST ===================

behavior@field_1   varchar(50) --行为描述
usersid@field_3   varchar(50) --用户SID
modulesid@field_5   varchar(50) --模块类型
appsid@field_6   varchar(50) --企业SID

=================== RETURN =================

<?xml version="1.0"?>
<root title="行为分析">
  <result msg="新增数据成功" code="200" time="3.9064毫秒" />
</root>

============================================
', N'/CAPool/PostCBehavior.aspx', N'', N'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>提交终端设备号</title>
<link href="/ACOM.css" type="text/css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="/ACOM.js"></script>
<script language="javascript" type="text/javascript">
function IntOther(aus, ppw, tks, w, h,skp){top.POP.ChangeSize(aid.GUParams()["cinf"], 350+26,380+42, true,document.title);}
</script>
</head>
<body onload="IntWin(1,1,0,'''')">
<form name="FormAid" id="FormAid" action="/CAPool/PostCBehavior.aspx" method="post" target="_blank">
<table width="330" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="60" colspan="2" align="center"><strong>收集用户行为(PostCBehavior)</strong></td>
    </tr>
  <tr>
    <td width="120" height="30" align="right">用户SID：</td>
    <td align="left"><input name="usersid" type="text" id="usersid" />
      </td>
  </tr>
  <tr>
    <td height="30" align="right">模块SID：</td>
    <td><input name="modulesid" type="text" id="modulesid" /></td>
  </tr>
  <tr>
    <td height="30" align="right">事物SID：</td>
    <td><input name="appsid" type="text" id="appsid" /></td>
  </tr>
  <tr>
    <td height="30" align="right">行为描述：</td>
    <td><textarea name="behavior" rows="5" id="behavior"></textarea></td>
  </tr>
  <tr>
    <td height="40">&nbsp;</td>
    <td><input type="submit" name="button" id="button" value="完 成" /></td>
  </tr>
</table>
</form>
</body>
</html>', N'', N'', CAST(0x0000A6250117E070 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (385, N'ZCM用户文件上传（PostFilesTS）', 29, N'', N'comclass_c373f99a5fe9435ea46f433402ff2ae6', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'A 获取上传密钥：
==================== GET ====================

DeviceToken：设备ID
AppName：应用名称(评论：0 ：1 ：2)

=================== RETURN =================

<?xml version="1.0"?>
<root title="用户上传接口A">
  <result DeviceToken="admin" AppLockID="tmp_aa84" msg="" code="200">2ee7c8a</result>
</root>

============================================


B 用户上传接口：
==================== GET ====================

LicenseID：上传许可ID

==================== POST ===================
AppLockID：锁定资源
FileTitle：图片标题
FileMark：图片描述
file[必填]：一个或多个文件域
FileFactions[必填]：文件分类（61:私家宝贝）
PicMinSize：缩略图大小(默认值为158*158)
=================== RETURN =================
<?xml version="1.0"?>
<root title="用户上传接口B">
  <result msg="" code="200">
    <list UploadCount="1" CreditAid="600" CountCredit="481" LicenseID="57500">
      <item fileName="6f37.jpg" ContentLength="48311" SFilePath="f1.jpg" />
    </list>
  </result>
</root>

============================================
', N'/AFU2012/PostFilesTS.aspx', N'', N'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户文件上传</title>
<link href="/ACOM.css" type="text/css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="/ACOM.js"></script>
<script language="javascript" type="text/javascript">
function IntOther(aus, ppw, tks, w, h,skp){top.POP.ChangeSize(aid.GUParams()["cinf"], 350+26,380+42, true,document.title);}
function UpdataFile(el){el.action += "?LicenseID=" + aid.$("LicenseID").value;}
function GetSelectPlanDataInt(e){
	try{
	var url=e.getAttributeNode("url").value;
	if(aid.$("DeviceToken").value==""){ alert("请先输入DeviceToken再操作!");return false;}
	e.getAttributeNode("nurl").value=url+((url.indexOf("?")==-1)?"?":"&")+"DeviceToken="+aid.$("DeviceToken").value;
	}catch(e){alert(e);}
	return true;
}
function MySetSelectPlanData(el){
	if(el.getAttribute("code")=="200") aid.$("LicenseID").value=el.childNodes[0].nodeValue;
	else alert(el.getAttribute("mes"));
}
</script>
</head>
<body scroll="no" onload="IntWin(1,'''',0,0)">
<form name="FormAid" id="FormAid" action="/AFU2012/PostFilesTS.aspx" method="post" enctype="multipart/form-data" target="_blank" onsubmit="UpdataFile(this)">
<table width="330" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="60" colspan="2" align="center"><strong>用户上传接口(GetLicenseID)</strong></td>
    </tr>
  <tr>
    <td width="120" height="36" align="right">设备编码：</td>
    <td align="left"><input name="DeviceToken" type="text" id="DeviceToken" value="iphone" />
      </td>
  </tr>
  <tr>
    <td height="36" align="right">&nbsp;</td>
    <td align="left"><input type="button" value="GetLicenseID" onclick="aid.GetSelectPlanData(event,MySetSelectPlanData)" url="/AFU2012/PostFilesTS.aspx" nurl="" /></td>
    </tr>
  <tr>
    <td height="20" colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" colspan="2" align="center"><strong>用户上传接口(PostFiles)</strong></td>
    </tr>
  <tr>
    <td height="30" align="right">锁定资源：</td>
    <td><input name="AppLockID" type="text" id="AppLockID" value="partner_2dbe9db157d54ab68917a3369a6aebc1" /></td>
  </tr>
  <tr>
    <td height="36" align="right">上传密钥：</td>
    <td><input type="text" name="LicenseID" id="LicenseID" /></td>
  </tr>
  <tr>
    <td height="36" align="right">文件A：</td>
    <td><input name="UFInput1" type="file"  id="UFInput1" size="14" contenteditable="false" /></td>
  </tr>
  <tr>
    <td height="36" align="right">文件B：</td>
    <td><input name="UFInput2" type="file"  id="UFInput2" size="14" contenteditable="false" /></td>
  </tr>
  <tr>
    <td height="40">&nbsp;</td>
    <td><input type="submit" name="button" id="button" value="PostFiles" /></td>
  </tr>
</table>
</form>
</body>
</html>', N'', N'', CAST(0x0000A625011D0DD4 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (386, N'ZCM获取短信验证（VerificationSMS）', 31, N'', N'comclass_f3e6c8e403cf4c798991681650d98ff4', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'获取短信验证
==================== GET ====================

mphone：手机号码
cmsg：（JZ:提示$vcode$信息；RL:模板ID）
job：（reg:用户注册；fpw:找回密码）

=================== RETURN =================

<?xml version="1.0"?>
<root title="用户上传接口A">
  <result DeviceToken="admin" AppLockID="tmp_aa84" msg="" code="200">2ee7c8a</result>
</root>

============================================

B 找回密码：
==================== GET ====================

LicenseID：上传许可ID

==================== POST ===================

FileTitle：图片标题
FileMark：图片描述
file[必填]：一个或多个文件域
FileFactions[必填]：文件分类（61:私家宝贝）
PicMinSize：缩略图大小(默认值为158*158)

=================== RETURN =================

<?xml version="1.0"?>
<root title="用户上传接口B">
  <result msg="" code="200">
    <list UploadCount="1" CreditAid="600" CountCredit="481" LicenseID="57500">
      <item fileName="6f37.jpg" ContentLength="48311" SFilePath="f1.jpg" />
    </list>
  </result>
</root>

============================================
', N'/CAPool/VerificationSMS.aspx', N'', N'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>获取短信验证</title>
<link href="/ACOM.css" type="text/css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="/ACOM.js"></script>
<script language="javascript" type="text/javascript">
function IntOther(aus, ppw, tks, w, h,skp){top.POP.ChangeSize(aid.GUParams()["cinf"], 350+26,380+42, true,document.title);}
</script>
</head>
<body onload="IntWin(1,1,0,'''')">
<form name="FormAid" id="FormAid" action="/CAPool/VerificationSMS.aspx" method="post" target="_blank">
<table width="330" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="60" colspan="2" align="center"><strong>获取短信验证（VerificationSMS.aspx）</strong></td>
    </tr>
  <tr>
    <td width="120" height="30" align="right">手机号码：</td>
    <td align="left"><input name="mphone" type="text" id="mphone" value="15800327113" />
      </td>
  </tr>
  <tr>
    <td height="30" align="right">获取方式：</td>
    <td><input name="job" type="text" id="job" value="reg" /></td>
  </tr>
  <tr>
    <td height="30" align="right">消息内容：</td>
    <td><textarea name="cmsg" rows="5" id="cmsg">您的验证码为：$vcode$。为孩子找老师,就上广晓哦！【广晓哦】</textarea></td>
  </tr>
  <tr>
    <td height="40">&nbsp;</td>
    <td><input type="submit" name="button" id="button" value="完 成" /></td>
  </tr>
</table>
</form>
</body>
</html>', N'', N'', CAST(0x0000A625012CCD14 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (387, N'ZCM客户端新用户注册（ClientRegister）', 18, N'', N'comclass_529ce73846d849b183675076d0ea445f', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'客户端新用户注册

==================== POST ===================

data：注册信息体
如：
{
"uid":"账号",
"pwd":"密码",
"dwc":"设备编码",
"mbp":"手机号码",
"tfn":"昵称",
"tjh":"推荐号码",
"role":"请到角色管理查看SID"
}

=================== RETURN =================

<?xml version="1.0"?>
<root title="客户端注册">
  <result msg="" code="200" time="38.0835毫秒">
    <userinf sid="RLA99O" rid="4443" userid="账号" headimg="" sexid="0" integral="0" birthday="" nickname="昵称" mobile="手机号码" email="" recommended="khNYzqLd" token="Ig5JWS3" department="" role="vvb" rolename="请到角色管理查看SID" addrsid="khNYzqLFvT33jzibr%2f1uYw%3d%3d" balance="0" storename="" homeaddr="" otherinf="" jdu="0.000" wdu="0.000" statu="0" cost="0" city="未知" district="" distsid="kh" myfoot="0" mycollect="0" address="" age="0" vip="0">
    </userinf>
  </result>
</root>

============================================
', N'/CAPool/ClientRegister.aspx', N'', N'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户注册(ClientRegister)</title>
<link href="/ACOM.css" type="text/css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="/ACOM.js"></script>
<script language="javascript" type="text/javascript">
function IntOther(aus, ppw, tks, w, h,skp){top.POP.ChangeSize(aid.GUParams()["cinf"], 600+26,530+42, true,document.title);}
function MySubmit(el){el.action += "?token=" + aid.$("token").value;}
</script>
</head>
<body onload="IntWin(1,1,0)">
<form name="FormAid" id="FormAid" action="/CAPool/ClientRegister.aspx" method="post" target="_blank" onsubmit="MySubmit(this)">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td height="20" colspan="2"></td>
    </tr>
    <tr>
      <td width="74" height="30" align="right">data：</td>
      <td width="526">注册信息体</td>
    </tr>
    <tr>
      <td height="30" colspan="2" align="center"><textarea style="width:90%;height:160px;" name="data" rows="5" id="data">{"uid":"账号","pwd":"密码","dsf":"OPID","dwc":"设备编码","mbp":"手机号码","tfn":"昵称","tjh":"推荐号码","role":"请到角色管理查看SID"}</textarea></td>
    </tr>
    <tr>
      <td height="22" align="right">xml：:</td>
      <td height="22">返回值</td>
    </tr>
    <tr>
      <td height="22" colspan="2" align="center">
      <textarea style="width:90%; height:180px;" name="data2" rows="5" id="data2">&lt;root title=&quot;客户端注册&quot;&gt;<br />
        &lt;result msg=&quot;&quot; code=&quot;200&quot; time=&quot;26.3655毫秒&quot;&gt;<br />
        &lt;userinf sid=&quot;JwokmJ2RstuU5ouIhLclgABLhVor&quot; rid=&quot;4440&quot; userid=&quot;ked&quot; headimg=&quot;&quot; sexid=&quot;0&quot; integral=&quot;0&quot; birthday=&quot;&quot; nickname=&quot;悟空&quot; mobile=&quot;158&quot; email=&quot;&quot; recommended=&quot;khNYzqLFvT33jzibr%2f1uYw%3d%3d&quot; token=&quot;4ALZcWArbNexjU&quot; department=&quot;&quot; role=&quot;VH2qUCRAp1%2fOrrkWizHqxw%3d%3d&quot; rolename=&quot;333&quot; addrsid=&quot;khNYzqLFvT33jzibr%2f1uYw%3d%3d&quot; balance=&quot;0&quot; storename=&quot;&quot; homeaddr=&quot;&quot; otherinf=&quot;&quot; jdu=&quot;0.0000&quot; wdu=&quot;0.0000&quot; statu=&quot;0&quot; cost=&quot;0&quot; city=&quot;未知&quot; district=&quot;&quot; distsid=&quot;khNYzqLFvT33jzibr%2f1uYw%3d%3d&quot; myfoot=&quot;0&quot; mycollect=&quot;0&quot; address=&quot;&quot; age=&quot;0&quot; vip=&quot;0&quot;&gt;&lt;/userinf&gt;<br />
        &lt;/result&gt;<br />
      &lt;/root&gt;</textarea></td>
    </tr>
    <tr>
      <td height="60" colspan="2" align="center"><input type="submit" name="button2" id="button2" value="PostFiles" /></td>
    </tr>
  </table>
</form>
</body>  
</html>', N'', N'', CAST(0x0000A62501391790 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (388, N'ZCM客户登录获取TOKEN（ClientLogin）', 27, N'', N'comclass_951cb6d4f575441f9ede61db3475414e', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'客户端新用户注册
==================== POST ===================
data：登录信息体

第三方账号：
{"dsf":"OPID","dwc":"设备号","tx":"头像","sex":"1","tfn":"昵称"}

站内账号：
{"uid":"账号","pwd":"登陆密码","dwc":"设备号"} 

手机短信登陆：
{"uid":"手机号","pwd":"SMS$短信密码","dwc":"设备号"}

uid { get; set; } //登录账号
pwd { get; set; } //登录密码
cde { get; set; } //验证号码
mbp { get; set; } //手机号码
tfn { get; set; } //真实姓名
dwc { get; set; } //设备编号
dsf { get; set; } //第三方ID
tx { get; set; } //头像
sex { get; set; } //性别
tjh { get; set; } //推荐号
role { get; set; } //角色
sid { get; set; } //锁定资源
mail { get; set; } //邮箱

=================== RETURN =================

<?xml version="1.0"?>
<root title="客户端登录">
  <result msg="" code="200" time="27.3420毫秒">
    <userinf sid="JwokmJ" rid="4440" userid="ked" headimg="" sexid="0" integral="0" birthday="" nickname="悟空" mobile="158" email="" recommended="khNY" token="cxJ" department="" role="VH2q" rolename="333" addrsid="khN" balance="0" storename="" homeaddr="" otherinf="" jdu="0.000" wdu="0.000" statu="0" cost="0" city="未知" district="" distsid="khN" myfoot="0" mycollect="0" address="" age="0" vip="0">
    </userinf>
  </result>
</root>

============================================
', N'/CAPool/ClientLogin.aspx', N'', N'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户登录</title>
<link href="/ACOM.css" type="text/css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="/ACOM.js"></script>
<script language="javascript" type="text/javascript">
function IntOther(aus, ppw, tks, w, h,skp){top.POP.ChangeSize(aid.GUParams()["cinf"], 350+26,440+42, true,document.title);}
function MySubmit(el){el.action += "?token=" + aid.$("token").value;}
</script>
</head>
<body onload="IntWin(1,1,0,'''')">
<form name="FormAid" id="FormAid" action="/CAPool/ClientLogin.aspx" method="post" target="_blank" onsubmit="MySubmit(this)">
  <table width="330" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td height="60" colspan="2" align="center"><strong>用户登录(ClientLogin)</strong></td>
    </tr>
    <tr>
      <td width="83" height="30" align="right">data：</td>
      <td width="247">登录信息体</td>
    </tr>
    <tr>
      <td height="30" colspan="2" align="center"><textarea style="width:80%" name="data" rows="5" id="data">{&quot;uid&quot;:&quot;kedll&quot;,&quot;pwd&quot;:&quot;123456&quot;,&quot;dwc&quot;:&quot;123&quot;}</textarea></td>
    </tr>
    <tr>
      <td height="22" colspan="2" style="border-bottom:#CCC dotted 2px;">&nbsp;</td>
    </tr>
    <tr>
      <td height="20" colspan="2" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td height="60" colspan="2" align="center"><span style="font-size:12px">第三方账号：<br />
        {&quot;dsf&quot;:&quot;第三OpenID&quot;,&quot;dwc&quot;:&quot;设备号&quot;,&quot;tx&quot;:&quot;头像地址&quot;,&quot;sex&quot;:&quot;1&quot;,&quot;tfn&quot;:&quot;昵称&quot;}<br />
        <br />
        站内账号：<br />
        {&quot;uid&quot;:&quot;账号&quot;,&quot;pwd&quot;:&quot;登陆密码&quot;,&quot;dwc&quot;:&quot;设备号&quot;} <br />
        <br />
        手机短信登陆：<br />
        {&quot;uid&quot;:&quot;手机号&quot;,&quot;pwd&quot;:&quot;SMS$短信密码&quot;,&quot;dwc&quot;:&quot;设备号&quot;} </span></td>
    </tr>
    <tr>
      <td height="22" colspan="2" style="border-bottom:#CCC dotted 2px;">&nbsp;</td>
    </tr>
    <tr>
      <td height="40" colspan="2" align="center"><input type="submit" name="button2" id="button2" value="PostFiles" /></td>
    </tr>
  </table>
</form>
</body>  
</html>', N'', N'', CAST(0x0000A625013CB558 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (389, N'ZCM修改个人信息（UpdClienter）', 17, N'', N'comclass_0dc091a3668b4e02948849ea06e03bb3', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================
token：令牌
==================== POST ===================
field_1：账号
field_3：头像
field_6：性别
field_10：生日
field_11：密码
field_12：姓名
field_13：角色SID
field_14：手机
field_15：邮箱
field_16：推荐人ID、qq
field_17：收货地址SID
field_18：第三方ID集（$WX_54432$$QQ_4FSAGFD$）
field_20：说明
field_23：身份证号
field_25：在线状态
field_27：支付密码
field_30：主连键，用于主动绑定某一资源
field_31：个人地址
field_32：区域SID获取整个树装SID图，影响f36的结果
field_37：json自定义对象用于扩展个人资料字段
field_40：标签认证（|x2||h1|）0表拒绝,1表通过,2表待审
============================================', N'/CAPool/UpdClienter.aspx', N'', N'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>提交个人信息(UpdClienter)</title>
<link href="/ACOM.css" type="text/css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="/ACOM.js"></script>
<script language="javascript" type="text/javascript">
function IntOther(aus, ppw, tks, w, h,skp){top.POP.ChangeSize(aid.GUParams()["cinf"], 360+26,550+42, true,document.title);}
function MySubmit(el){el.action += "?hi=" + aid.TTime();}
</script>
</head>
<body onload="IntWin(1,1,0,'''')">
<form name="FormAid" id="FormAid" action="/CAPool/UpdClienter.aspx" method="post" target="_blank" onsubmit="MySubmit(this)">
  <table width="330" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td height="15" colspan="2" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td width="100" height="28" align="right">token：</td>
      <td align="left"><input name="token" type="text" id="token" style="width:90%" value="D5DN8XLt4ILm3oU6Mz9pSvd9JENfjCY2jOCBydTPO%2blWddGeUb1CmX89p%2fTEOZbe" /></td>
    </tr>
    <tr>
      <td height="28" align="right">密码：</td>
      <td align="left"><input name="field_11" type="password" id="field_11" style="width:90%" value="123456" /></td>
    </tr>
    <tr>
      <td height="28" align="right">姓名：</td>
      <td align="left"><input name="field_12" type="text" id="field_12" style="width:90%" value="张三" /></td>
    </tr>
    <tr>
      <td height="28" align="right">角色SID：</td>
      <td align="left"><input name="field_13" type="text" id="field_13" style="width:90%" value="systemrole_2e42c6d1c7404c5ba929f4b729bfe15d" /></td>
    </tr>
    <tr>
      <td height="28" align="right">头像：</td>
      <td align="left"><input name="field_3" type="text" id="field_3" style="width:90%" value="UPath/2014/4/28/211918889924457UFI.jpg" /></td>
    </tr>
    <tr>
      <td height="28" align="right">性别：</td>
      <td align="left"><input name="field_6" type="text" id="field_6" style="width:90%" value="2" /></td>
    </tr>
    <tr>
      <td height="28" align="right">生日：</td>
      <td><input name="field_10" type="text" id="field_10" style="width:90%" value="1994-1-1" /></td>
    </tr>
    <tr>
      <td height="28" align="right">手机：</td>
      <td><input name="field_14" type="text" id="field_14" style="width:90%" value="15800327113" /></td>
    </tr>
    <tr>
      <td height="28" align="right">推荐人ID：</td>
      <td><input name="field_16" type="text" id="field_16" style="width:90%" value="29933123" /></td>
    </tr>
    <tr>
      <td height="28" align="right">邮箱：</td>
      <td><input name="field_15" type="text" id="field_15" style="width:90%" value="29933123@qq.com" /></td>
    </tr>
    <tr>
      <td height="28" align="right">身份证：</td>
      <td><input name="field_31" type="text" id="field_31" style="width:90%" value="上海市浦东新区" /></td>
    </tr>
    <tr>
      <td height="28" align="right">收货地址SID：</td>
      <td><input name="field_17" type="text" id="field_17" style="width:90%" value="gfdsgfdsgdsf" /></td>
    </tr>
     <tr>
      <td height="28" align="right">居住地址：</td>
      <td><input name="field_23" type="text" id="field_23" style="width:90%" value="3623311982" /></td>
    </tr>
    <tr>
      <td height="28" align="right">介绍：</td>
      <td><input name="field_20" type="text" id="field_20" style="width:90%" value="" /></td>
    </tr>
     <tr>
      <td height="28" align="right">json数据：</td>
      <td height="30" colspan="2" align="left"><textarea style="width:90%" name="field_37" rows="5" id="field_37">[{"a":"真实姓名","b":"大学地址","c":"大学名称","d":"宿舍信息"}]</textarea></td>
    </tr>
    <tr>
      <td height="40">&nbsp;</td>
      <td><input type="submit" name="button2" id="button2" value="PostFiles" /></td>
    </tr>
  </table>
</form>
</body>  
</html>', N'', N'', CAST(0x0000A6250140337C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (390, N'公司部门', 0, N'', N'comclass_c28775c4915640d2991ca7603fc0bc05', N'0', N'', N'', N'', N'GongSiBuMen', N'', N'', N'', CAST(0x0000A625016C5EE8 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (391, N'List_Pub客户信息列表（clit_）', 0, N'', N'comclass_7270b8cb9bb24c4894c01be2aafae5ec', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================

psize（每页大小：10-1000）
lastmd5（数据校验）
pos（第几页：1为默认值）
AppType(事物类型)
LngLat（经纬度或auto）


_byAreaSid：区域SID
_byRoleSid：角色SID
_bySexId：性别ID（保密：0，男：1，女：2）
_byAges：年龄（4-5）
_byObjSid：课程SID
_byLPrice：按最低价排
_byHPrice：按最高价排
_byShangMen：（无参数）

==================== 用例 ====================

/AMAPI/DListCenter.aspx?AppType=clit_byRoleSidI6kw86g92vQuhQ84apcKVLA52JjsIHffmg3UGBsZpnjUMtgcNejEuKTArXNgZSRh

=================== RETURN =================

<?xml version="1.0"?>
<root title="列表查询中心">
  <result msg="" code="200" time="8.7894毫秒">
    <list md5="" RQPath="/UtilAPI/DListCenter.aspx?AppType=&amp;" SumRecord="30" SumPage="1" pos="1" PageSize="30" WhereStr="''" AppName="客户_按角色(传入SID)_">
      <item num="1" id="4437" sid="%2bNFF" tsid="" ApType="" title="" mkey="123456" />
      <item num="2" id="4434" sid="%2bkcagR5%%" tsid="" ApType="" title="　仙呵呵。" mkey="" />
    </list>
  </result>
</root>
============================================', N'AMAPI/DListCenter.aspx', N'', N'', N'', N'', CAST(0x0000A6280169C41C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (392, N'公司动态', 0, N'', N'comclass_71da9f29f6cd4c2880a6a903e1039724', N'comclass_f7085d7f0b404968bf17d24d0e483b6b', N'|End0||comclass_f7085d7f0b404968bf17d24d0e483b6b|', N'', N'', N'GongSiDongTai', N'', N'', N'', CAST(0x0000A628016F0314 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (402, N'ZCM用经纬度取城市SID（GetCityByWJ）', 30, N'', N'comclass_fff94d6a69df41338850475797e62e0f', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================

wd：纬度
jd：经度

==================== 用例 ====================
/CAPool/GetCityByWJ.aspx?wd=31.213382&jd=121.530216
=================== RETURN =================
<?xml version="1.0"?>
<root title="根据经纬度取城市">
  <result msg="上海市" code="200" time="19.5320毫秒">
    <inf sid="lwovPX67B8xfn1UCp7fyQ%2bnIXyHlm4h56v8qLl9xzrYQOUlSwbLDN7fenGTq%2fOfd" name="上海市" />
  </result>
</root>
============================================
', N'CAPool/GetCityByWJ.aspx', N'', N'', N'', N'', CAST(0x0000A628018A7220 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (403, N'ZCM网络心跳（KeepHeart）', 33, N'', N'comclass_11aea6040f5043e1a2f26f8ad3008b48', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================

data：用户数据{
"tok":"登陆令牌或设备号",
"dwc":"设备号",
"jd":"经度",
"wd":"纬度",
"city":"城市名称"
}
==================== 用例 ====================
/CAPool/KeepHeart.aspx?data={"tok":"7b481c3ab4558147ec14c1aac433750308763f20180c42e2ede89e1426e1e072","dwc":"dfs","jd":"22.88","wd":"22.99","city":"上海市"}
=================== RETURN =================

<?xml version="1.0"?>
<root title="保持在线心跳">
  <result cityid="gaddress_f1adb" citysid="lwovP" msg="ok" code="200" time="14.6475毫秒">
    <list SumRecord="0" />
  </result>
</root>

============================================
', N'/CAPool/KeepHeart.aspx ', N'', N'', N'', N'', CAST(0x0000A6290007A058 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (404, N'List_Pub城市地区列表（gdrs_）', 16, N'', N'comclass_56005fa584574ddfb43af9cecde1cf40', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================

psize（每页大小：10-1000）
lastmd5（数据校验）
pos（第几页：1为默认值）
AppType(事物类型)

（_byPrtNm：父级名称或SID）
（_byClsNum：第几级）
（_byAppID：应用ID）
（_byKWords：搜索关键字）

==================== 用例 ===================

/AMAPI/DListCenter.aspx?pos=1&psize=0&AppType=gdrs_byPrtNmlwovPX67B8xfn1UCp7fyQ%2bnIXyHlm4h56v8qLl9xzrYQOUlSwbLDN7fenGTq%2fOfd_byClsNum3_byAppID11

=================== RETURN =================

<?xml version="1.0"?>
<root title="列表查询中心">
  <result msg="" code="200" time="3.9060毫秒">
    <list md5="509D" RQPath="/UtilAPI/DListCenter.aspx?psize=8&amp;AppType=gdrs_byPrtNm上海市&amp;" SumRecord="19" SumPage="3" pos="1" PageSize="8" WhereStr="field_5 = ''gaddress_f1adbdc'' " AppName="地区_父名取子类(gaddress_f1adbdecac)_">
      <item num="1" id="79" sid="MWDTOcPCo%" tsid="" ApType="200" title="黄浦区" img="">0</item>
      <item num="3" id="77" sid="%%" tsid="" ApType="200" title="徐汇区" img="">0</item>
    </list>
  </result>
</root>

============================================
', N'AMAPI/DListCenter.aspx', N'', N'', N'', N'', CAST(0x0000A62900980148 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (407, N'List_Pub综合分类列表（ccls_）', 0, N'', N'comclass_192651dd402c498798ff7bbd55e2cc6b', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================

psize（每页大小：10-1000）
lastmd5（数据校验）
pos（第几页：1为默认值）
AppType(steach_)

（_byPrtNm*=父级名的子类) 
（_byClsNum：第几级）
（_byKWords=搜索关键字) 
（_byBindFile*=加入相关文件) 
（_byOSClass*=加入一层子集) 
（_byTextContent*=加入说明) 
（_bySBC*=加入子级统计) 
（_byBindLibNS=加入绑定新闻资源) 
（_byMert=根据订用者SID查出内部分类) 

==================== 用例 ===================

查年级下面的第一层子集
AMAPI/DListCenter.aspx?AppType=ccls_byPrtNmwejUKABhBx6phL%2fzaOeYOWTIyX2O0i17W74kEmPj1aTmKvJtL%2bd3fPgDNL29wuH2_byClsNum1DD

查年级下面的第二层子集
AMAPI/DListCenter.aspx?AppType=ccls_byPrtNmwejUKABhBx6phL%2fzaOeYOWTIyX2O0i17W74kEmPj1aTmKvJtL%2bd3fPgDNL29wuH2_byClsNum2DD

=================== RETURN =================

<?xml version="1.0"?>
<root title="列表查询中心">
  <result msg="" code="200" time="8.7894毫秒">
    <list md5="" RQPath="/UtilAPI/DListCenter.aspx?AppType=&amp;" SumRecord="30" SumPage="1" pos="1" PageSize="30" WhereStr="''" AppName="客户_按角色(传入SID)_">
      <item num="1" id="4437" sid="%2bNFF" tsid="" ApType="" title="" mkey="123456" />
      <item num="2" id="4434" sid="%2bkcagR5%%" tsid="" ApType="" title="　仙呵呵。" mkey="" />
    </list>
  </result>
</root>
============================================', N'AMAPI/DListCenter.aspx', N'', N'', N'', N'', CAST(0x0000A62A01624DF4 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (408, N'婚宴部', 0, N'', N'comclass_d68ef8ca1f1148d99338eb5ba05961c8', N'comclass_c28775c4915640d2991ca7603fc0bc05', N'|End0||comclass_c28775c4915640d2991ca7603fc0bc05|', N'', N'', N'HunYanBu', N'', N'', N'', CAST(0x0000A62B00C0F030 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (409, N'婚庆部', 0, N'', N'comclass_9c3ac88ef6ab4a0da34d50f4ef2c9d24', N'comclass_c28775c4915640d2991ca7603fc0bc05', N'|End0||comclass_c28775c4915640d2991ca7603fc0bc05|', N'', N'', N'HunQingBu', N'', N'', N'', CAST(0x0000A62B00C10098 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (411, N'PCmd_Pub通用删除记录（DelSelfRcd）', 0, N'', N'comclass_b03ea55029da40768d5c577fb449357e', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================
必选项--token：（令牌）
必选项--cmd：DelSelfRcd
必选项--data：sid$$权力号（详见数据结构说明）
==================== 用例 ===================
AMAPI/SimplePlanCmd.aspx?cmd=DelSelfRcd&data=Fb%2bZmpagNpzS%2bN1JSCC1lP3BPYskmIU%2fAz8ixVdkfEhNPaTlq8nb61Knei32NXZm$$6&token=%2b20AV8D%2fTuD6Z2E2AyoTx7UQvrwOJBOx8HzCv%2fsKF0OYXm%2bjG30M186tJrPFLlRK', N'AMAPI/SimplePlanCmd.aspx', N'', N'', N'', N'', CAST(0x0000A62B01384BA8 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (413, N'用户上传', 0, N'', N'comclass_c748ab47be1847689849c0845fe39598', N'comclass_a147694cb58347de80d9e1565220a6d3', N'|comclass_a147694cb58347de80d9e1565220a6d3|', N'', N'', N'YongHuShangChuan', N'', N'', N'', CAST(0x0000A62D0109A2BC AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (415, N'实名认证', 0, N'', N'comclass_a629c956f4294bceb5b7ad19ca147ce3', N'comclass_c748ab47be1847689849c0845fe39598', N'|comclass_a147694cb58347de80d9e1565220a6d3||comclass_c748ab47be1847689849c0845fe39598|', N'', N'shiming', N'ShiMingRenZheng', N'', N'', N'', CAST(0x0000A62D010A46CC AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (418, N'ZCM附件管理接口（UserFAPI）', 25, N'', N'comclass_127d18cf35be4a0f85480516cfbd5c45', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================
token
cmd（list）data（分类SID）
cmd（delt）data（文件ID）
ftype（分类sid）
==================== 用例 ===================
列表：
/CAPool/UserFAPI.aspx?token=zd%2fnOfMgiMK6sUfFxDaot5DDovKbTOncOiMS9pkha6yNECqT51nZcZzPgQP8v%2f6Z&cmd=list&ftype=comclass_5da01840d69649a085ff4f6cc50524b9

删除：
/CAPool/UserFAPI.aspx?token=zd%2fnOfMgiMK6sUfFxDaot5DDovKbTOncOiMS9pkha6yNECqT51nZcZzPgQP8v%2f6Z&cmd=delt&fid=5150', N'/CAPool/UserFAPI.aspx', N'', N'', N'', N'', CAST(0x0000A62D011F1E58 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (419, N'List_Pub广告信息列表（adi_）', 7, N'', N'comclass_162066caf5304da9b7835620b93c2cc6', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================

psize（每页大小：10-1000）
lastmd5（数据校验）
pos（第几页：1为默认值）

AppType(事物类型)
_bySClsID：标签类别SID（多选）
_bySClsID：内容类别SID（单选）
_byUserID：客户SID
_byUserRT：某种关系（要再用户TOKEN）详见《更新绑定》接口

==================== 用例 ====================

/AMAPI/DListCenter.aspx?AppType=news_byUserRT100&token=h4WQfnrn6osvXHpiWfRQ2fBkQ9VWPsgsrles4HrMdZ0EqW7gw3X9jrVpobECIzkP

=================== RETURN =================

============================================', N'/AMAPI/DListCenter.aspx', N'', N'', N'', N'', CAST(0x0000A62E017F84DC AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (420, N'List_Pub互动返馈列表（cgms_）', 10, N'', N'comclass_bfddf47eb3b14bd99cb0b91f1d3c07d1', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================
psize（默认为最大记录数）
lastmd5（数据校验）
pos（默认值为1）

AppType(steach_)
_byStatu：审核通过的
_byCltSid：用户SID
_byAppSid：对像SID
_byCateId：应用类型（意见返馈|0*人员评论|1*疑惑评论|2）

==================== 用例 ===================
/AMAPI/DListCenter.aspx?AppType=cgms_byCltSid_byAppSid_byCateId_byStatu
=================== RETURN =================

<?xml version="1.0"?>
<root title="列表查询中心">
  <result msg="" code="200" time="8.7894毫秒">
    <list md5="" RQPath="/UtilAPI/DListCenter.aspx?AppType=&amp;" SumRecord="30" SumPage="1" pos="1" PageSize="30" WhereStr="''" AppName="客户_按角色(传入SID)_">
      <item num="1" id="4437" sid="%2bNFF" tsid="" ApType="" title="" mkey="123456" />
      <item num="2" id="4434" sid="%2bkcagR5%%" tsid="" ApType="" title="　仙呵呵。" mkey="" />
    </list>
  </result>
</root>
============================================', N'/AMAPI/DListCenter.aspx', N'', N'', N'', N'', CAST(0x0000A62F007F434C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (422, N'List_Pub新闻资讯列表（nsif_）', 4, N'', N'comclass_92e94db74d8b415cb1ca661b959d01e4', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================

psize（每页大小：10-1000）
lastmd5（数据校验）
pos（第几页：1为默认值）

AppType(事物类型)
_bySClsID：标签类别SID（多选）
_bySClsID：内容类别SID（单选）
_byUserID：客户SID
_byUserRT：某种关系（要再用户TOKEN）详见《更新绑定》接口

==================== 用例 ====================

/AMAPI/DListCenter.aspx?AppType=news_byUserRT100&token=h4WQfnrn6osvXHpiWfRQ2fBkQ9VWPsgsrles4HrMdZ0EqW7gw3X9jrVpobECIzkP

=================== RETURN =================

============================================', N'/AMAPI/DListCenter.aspx', N'', N' 请输入数据信息！', N'', N'', CAST(0x0000A63400DE31A4 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (423, N'List_Pub积分列表（userjf_）', 1, N'', N'comclass_877b873649654b9b86518c22d21fe9b1', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================

psize（每页大小：10-1000）
lastmd5（数据校验）
pos（第几页：1为默认值）

AppType(事物类型)
_byAppID：应用ID

==================== 用例 ====================

/AMAPI/DListCenter.aspx?AppType=userjf_byAppID8&token=h4WQfnrn6osvXHpiWfRQ2fBkQ9VWPsgsrles4HrMdZ0EqW7gw3X9jrVpobECIzkP

=================== RETURN =================

============================================', N'AMAPI/DListCenter.aspx', N'', N'', N'', N'', CAST(0x0000A634010B3384 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (424, N'ZCM验证码获权（GetTokenBySMS）', 26, N'', N'comclass_8c7e25480e004a3f8cb3fcc2de98ee48', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'mphone：手机号码(可选项)
email：绑定邮箱(可选项)
smscode：短信验证码


用例：
/CAPool/GetTokenBySMS.aspx?mphone=15800327113&smscode=890284


<?xml version="1.0"?>
 <root title="验证码获权">
 <result msg="" code="200">
 <userinf token="JC6lAsDifYcNiwlIW8ABL91SY%2f0Iu%2fCPbO7E5WeS4dAHWln75HZzNMTMn1wPTRfx" />
 </result>
 </root>', N'/CAPool/GetTokenBySMS.aspx', N'', N'', N'', N'', CAST(0x0000A63600F9F060 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (425, N'ZCM简单文档获取（ComHtmlPage）', 19, N'', N'comclass_351c82ad56044beba33a55e4474541a4', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ===================

pagename：类型（浏览量-ServiceAgreement；点赞量-good）

==================== 用例 ====================

CAPool/ComHtmlPage.aspx?pagename=ServiceAgreement', N'/CAPool/ComHtmlPage.aspx', N'', N'', N'', N'', CAST(0x0000A658013E717C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (427, N'ZCM更新绑定（UpdUABinder）', 21, N'', N'comclass_8bfe026d30b14773bac30eeff70e3d18', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'token：登陆令牌
cmd：操作方式（adit，delt，selt）
field_3：主叫类型（100关注收藏，200被赞）
field_5：主叫ID（用户A）
field_6：被叫ID（产品A）
field_8 ：备注
field_9：关系状态

==================== 用例 ===================
添加修改：
/CAPool/UpdUABinder.aspx?token=h4WQfnrn6osvXHpiWfRQ2fBkQ9VWPsgsrles4HrMdZ0EqW7gw3X9jrVpobECIzkP&cmd=adit&field_3=50&field_6=clienter_0c786ca5daad491d963e5dc175195acb

删除：
/CAPool/UpdUABinder.aspx?token=h4WQfnrn6osvXHpiWfRQ2fBkQ9VWPsgsrles4HrMdZ0EqW7gw3X9jrVpobECIzkP&cmd=delt&field_3=50&field_6=clienter_0c786ca5daad491d963e5dc175195acb

查询关系：
/CAPool/UpdUABinder.aspx?token=h4WQfnrn6osvXHpiWfRQ2fBkQ9VWPsgsrles4HrMdZ0EqW7gw3X9jrVpobECIzkP&cmd=selt&field_3=40&field_6=clienter_0c786ca5daad491d963e5dc175195acb', N'/CAPool/UpdUABinder.aspx', N'', N'', N'', N'', CAST(0x0000A658013FA6DC AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (428, N'ZCM计数统计中心（UICounter）', 22, N'', N'comclass_2e04f15b804a48c1bc423ddf57904f50', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'计数统计中心：
==================== POST ===================

type：类型（浏览量-read；点赞量-good）
appid ：事物SID（）
fdname：计数字段名
isrprt：是否重复统计（0不重复，1重复）

==================== 用例 ====================
浏览量：
/CAPool/UICounter.aspx?token=h4WQfnrn6osvXHpiWfRQ2fBkQ9VWPsgsrles4HrMdZ0EqW7gw3X9jrVpobECIzkP&type=read&fdname=field_7&appid=news_1707a256949942e9981820693781b1de

点赞量：
/CAPool/UICounter.aspx?token=h4WQfnrn6osvXHpiWfRQ2fBkQ9VWPsgsrles4HrMdZ0EqW7gw3X9jrVpobECIzkP&type=read&fdname=field_22&appid=news_1707a256949942e9981820693781b1de&isrprt=0
=================== RETURN =================

<?xml version="1.0"?>
<root title="行为分析">
  <result msg="新增数据成功" code="200" time="3.9064毫秒" />
</root>

============================================
', N'CAPool/UICounter.aspx', N'', N'', N'', N'', CAST(0x0000A65801400F28 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (430, N'ZCM卡券操作（CouponCMD）', 24, N'', N'comclass_1b56f4ef492b44a3929cf29b403aeb4e', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================
token：令牌
cmd：令牌（bind，used）
data：卡券号

绑定：
CAPool/CouponCMD.aspx?cmd=bind&data=f22d5291ba6240d6&token=udJ8HXvWSQzBOVzn92MtHGvZjt65Vi90G%2byIq7kSmw%2bWlfhT0OVWHV5cnf96qomv

使用：
CAPool/CouponCMD.aspx?cmd=used&data=f22d5291ba6240d6&token=udJ8HXvWSQzBOVzn92MtHGvZjt65Vi90G%2byIq7kSmw%2bWlfhT0OVWHV5cnf96qomv', N'CAPool/CouponCMD.aspx', N'', N'', N'', N'', CAST(0x0000A6580140AD5C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (431, N'List_Pub用户地址列表（useraddr_）', 13, N'', N'comclass_4bee69d8f1b9421c85f12991ea31600b', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================
psize（默认为最大记录数）
lastmd5（数据校验）
pos（默认值为1）

token：登陆令牌

==================== 用例 ===================
/AMAPI/DListCenter.aspx?AppType=useraddr_&token=4wI8RyEI2KYvMWvP%2bnoVKyAaaM8uYVrwPAe9MCGmxd9ZPf2hVSoBGdLwZH4SIjgw
=================== RETURN =================

<?xml version="1.0"?>
<root title="列表查询中心">
  <result msg="" code="200" time="8.7894毫秒">
    <list md5="" RQPath="/UtilAPI/DListCenter.aspx?AppType=&amp;" SumRecord="30" SumPage="1" pos="1" PageSize="30" WhereStr="''" AppName="客户_按角色(传入SID)_">
      <item num="1" id="4437" sid="%2bNFF" tsid="" ApType="" title="" mkey="123456" />
      <item num="2" id="4434" sid="%2bkcagR5%%" tsid="" ApType="" title="　仙呵呵。" mkey="" />
    </list>
  </result>
</root>
============================================', N'/AMAPI/DListCenter.aspx', N'', N'', N'', N'', CAST(0x0000A65801420134 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (432, N'List_Pub推送消息列表（insdmsg_）', 15, N'', N'comclass_ea7fabaae433484dbfc37b54ab093a34', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================
token：登陆令牌
psize（默认为最大记录数）
lastmd5（数据校验）
pos（默认值为1）
AppType(steach_)
_byStatu：审核通过的
_byCltSid：用户SID
_byAppSid：对像SID
_byCateId：应用类型（详见SimpleStatus>MsgType）
==================== 用例 ===================
/AMAPI/DListCenter.aspx?AppType=cgms_byCltSid_byAppSid_byCateId_byStatu
=================== RETURN =================

<?xml version="1.0"?>
<root title="列表查询中心">
  <result msg="" code="200" time="8.7894毫秒">
    <list md5="" RQPath="/UtilAPI/DListCenter.aspx?AppType=&amp;" SumRecord="30" SumPage="1" pos="1" PageSize="30" WhereStr="''" AppName="客户_按角色(传入SID)_">
      <item num="1" id="4437" sid="%2bNFF" tsid="" ApType="" title="" mkey="123456" />
      <item num="2" id="4434" sid="%2bkcagR5%%" tsid="" ApType="" title="　仙呵呵。" mkey="" />
    </list>
  </result>
</root>
============================================', N'/AMAPI/DListCenter.aspx', N'', N'', N'', N'', CAST(0x0000A6580142245C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (433, N'PCmd_Pub获取客户信息（GetUserInf）', 0, N'', N'comclass_c9c725dab148484d8a8f739699650b00', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================
必填项--cmd（GetUserInf）
可选项--token：（令牌）
可选项--data（客户sid）
==================== 用例 ===================
AMAPI/SimplePlanCmd.aspx?cmd=GetUserInf&data=Fb%2bZmpagNpzS%2bN1JSCC1lP3BPYskmIU%2fAz8ixVdkfEhNPaTlq8nb61Knei32NXZm&token=%2b20AV8D%2fTuD6Z2E2AyoTx7UQvrwOJBOx8HzCv%2fsKF0OYXm%2bjG30M186tJrPFLlRK', N'AMAPI/SimplePlanCmd.aspx', N'', N'', N'', N'', CAST(0x0000A6580142D424 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (434, N'PCmd_Pub获取对像的附件数量（GetFileNum）', 0, N'', N'comclass_a691d50715e042308fdbd7ff7d6897bf', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================
cmd：GetFileNum
data：对像的SID
==================== 用例 ===================', N'AMAPI/SimplePlanCmd.aspx', N'', N'', N'', N'', CAST(0x0000A6580142F74C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (435, N'PCmd_Pub获取对像的关系数量（GeBindNum）', 0, N'', N'comclass_454473a11091479d837d6a489410814c', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================
cmd：GeBindNum
data：对像的SID
==================== 用例 ===================', N'AMAPI/SimplePlanCmd.aspx', N'', N'', N'', N'', CAST(0x0000A65801431A74 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (436, N'PCmd_Pub用户扫码充值（SumbQrcode）', 0, N'', N'comclass_829cc6cb9f4a4975a53f55024b57f0e9', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================
必选项--token：（令牌）
必选项--cmd：SumbQrcode
必选项--data：充值ID
==================== 用例 ===================
AMAPI/SimplePlanCmd.aspx?cmd=SumbQrcode&data=充值ID&token=%2b20AV8D%2fTuD6Z2E2AyoTx7UQvrwOJBOx8HzCv%2fsKF0OYXm%2bjG30M186tJrPFLlRK', N'AMAPI/SimplePlanCmd.aspx', N'', N'', N'', N'', CAST(0x0000A65801434E04 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (437, N'PCmd_Pub设置用户登陆状态（SCUserStatu）', 0, N'', N'comclass_33f965c67f0f4c71b9e3c8bfd3c16659', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================
必选项--token：（令牌）
必选项--cmd：SCUserStatu
可选项--data：状态值（0离线，1在线，2隐身）
==================== 用例 ===================
AMAPI/SimplePlanCmd.aspx?cmd=field_26&data=2&token=%2b20AV8D%2fTuD6Z2E2AyoTx7UQvrwOJBOx8HzCv%2fsKF0OYXm%2bjG30M186tJrPFLlRK', N'AMAPI/SimplePlanCmd.aspx', N'', N'', N'', N'', CAST(0x0000A658014360C4 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (439, N'PCmd_Pub某类最新资讯（GTONewsInf）', 0, N'', N'comclass_315359c9a372411798cd8d8e641e8c59', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================
必选项--cmd：GTONewsInf
必选项--data：用户SID
==================== 用例 ===================
/AMAPI/SimplePlanCmd.aspx?cmd=GTONewsInf&data=9LF3B5FcPtAPPrf8', N'/AMAPI/SimplePlanCmd.aspx', N'', N'', N'', N'', CAST(0x0000A65B00ABC69C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (440, N'PCmd_Pub添加积分（AddUIntegral）', 0, N'', N'comclass_21afb9562ac146e3ac70bb5b70a434c6', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==================== GET ====================

cmd：AddUIntegral
data：{jifen,appid,msg}

==================== 用例 ===================
/AMAPI/SimplePlanCmd.aspx?cmd=AddUIntegral&data={"jifen":"100","appid":"0","msg":"好评加分"}&token=KYvDyX6EEZzhKpktiMsx6pEDMKXT5PZugKPITZfWQGLPr%2fuoZNkgfS0MGqgI0%2fwU', N'AMAPI/SimplePlanCmd.aspx', N'', N'', N'', N'', CAST(0x0000A65C0101A24C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (1438, N'行业分类', 0, N'', N'comclass_70ad81c211a04bdc90e16f22d1454507', N'0', N'|End0|', N'行业大分类', N'', N'XingYeFenLei', N'行业大分类', N'', N'', CAST(0x0000A68500B4F30C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (1439, N'IT行业', 0, N'', N'comclass_a973531686aa4f899609e897147860db', N'comclass_70ad81c211a04bdc90e16f22d1454507', N'|End0||comclass_70ad81c211a04bdc90e16f22d1454507|', N'IT行业', N'', N'', N'IT行业', N'', N'', CAST(0x0000A68500B5D8BC AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (1440, N'销售行业', 0, N'', N'comclass_71aa52ff797f4e98bba325380799a4d1', N'comclass_70ad81c211a04bdc90e16f22d1454507', N'|End0||comclass_70ad81c211a04bdc90e16f22d1454507|', N'销售行业', N'', N'', N'销售行业', N'', N'', CAST(0x0000A68500B79F6C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (1441, N'好友分组', 0, N'', N'comclass_c67641d52bb0446cbefd5c2c4663fede', N'0', N'|End0|', N'', N'', N'HaoYouFenZu', N'', N'', N'', CAST(0x0000A68E00CCCC84 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (1443, N'形像照片', 0, N'', N'comclass_1e477585bfde4a60bd4718795c97e3a6', N'comclass_c748ab47be1847689849c0845fe39598', N'|End0||comclass_a147694cb58347de80d9e1565220a6d3||comclass_c748ab47be1847689849c0845fe39598|', N'', N'gallery', N'XingXiangZhaoPian', N'', N'|1||2|', N'', CAST(0x0000A68E00D6C284 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (1444, N'通用接口测试工具', 0, N'', N'comclass_bdf786458e504ba3b6c88d438f0f4eb8', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'通用接口测试工具', N'', N'', N'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户注册(ClientRegister)</title>
<link href="/ACOM.css" type="text/css" rel="stylesheet">
<script language="javascript" type="text/javascript" src="/ACOM.js"></script>
<script language="javascript" type="text/javascript">
function IntOther(aus, ppw, tks, w, h,skp){
	top.POP.ChangeSize(kd.GUParams()["cinf"], 600+26,530+42, true,document.title);
	kd.$("api").value=kd.GUParams()["api"].replace(/\$/g, ''/'');
	kd.$("data").value+=decodeURIComponent(kd.GUParams()["apidata"]).replace(/\$/g, ''\n'').replace(/~/g, ''='');
}
function MySubmit(el)
{
	var data=kd.$("data").value.replace(/\n/g, ''&'');
	data = kd.ASParam2(data, "token",kd.$("token").value);
	var Url = kd.$("api").value;Url = kd.AParam(Url, "hi=" + kd.TTime());
	kd.LoadXMLFile(Url, data, true, "result");
}
function OpenWin(){
	var api=kd.GUParams()["api"].split("$")[1].replace("API.aspx","_");
	window.open("/AMAPI/DListCenter.aspx?pos=1&psize=8&token=&AppType="+api);
}
</script>
</head>
<body onload="IntWin(1,1,0)">
<form name="FormAid" id="FormAid" action="" method="post" target="_blank" onsubmit="MySubmit(this)">
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td height="50" colspan="2" align="center" style="color:#060; font-size:18px; font-weight:bold;">通用接口测试工具</td>
    </tr>
    <tr>
      <td height="30" align="right">Token：</td>
      <td><label for="textfield2"></label>
        <input type="text" name="token" id="token" style="width:490px;" /></td>
    </tr>
    <tr>
      <td height="30" align="right">Url：</td>
      <td><label for="textfield"></label>
      <input type="text" name="api" id="api" style="width:490px;" /></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top">Post：</td>
      <td><textarea style="width:490px;height:250px;" name="data" rows="5" id="data"></textarea></td>
    </tr>
    <tr>
      <td height="20" align="right" valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="74" height="30" align="right" valign="top">Return：</td>
      <td width="526" valign="top"><textarea style="width:490px;height:80px;" name="result" rows="5" id="result"></textarea></td>
    </tr>
    <tr>
      <td height="50" colspan="2" align="center"><input type="button" name="Submit" id="Submit" value="提交数据" onclick="MySubmit()" />
      <input type="button" name="Submit2" id="Submit2" value="列表接口" onclick="OpenWin()" /></td>
    </tr>
  </table>
</form>
</body>  
</html>', N'', N'', CAST(0x0000A6A90115FDA0 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (1445, N'图形验证问题', 0, N'', N'comclass_ac6c5f7023d147f4ab84e7cdc2c1452a', N'0', N'|End0|', N'课程主题图', N'', N'TuXingYanZhengWenTi', N'', N'', N'', CAST(0x0000A6A901173EB8 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (1446, N'数学', 0, N'', N'comclass_e506e84a41534e739ea5a1bccc5f4a6a', N'comclass_ac6c5f7023d147f4ab84e7cdc2c1452a', N'|End0||comclass_ac6c5f7023d147f4ab84e7cdc2c1452a|', N'', N'', N'ShuXue', N'', N'', N'', CAST(0x0000A6A901176A14 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (1447, N'历史', 0, N'', N'comclass_ba9f67e4b7614283a8d85ba0da6c2c4b', N'comclass_ac6c5f7023d147f4ab84e7cdc2c1452a', N'|End0||comclass_ac6c5f7023d147f4ab84e7cdc2c1452a|', N'', N'', N'LiShi', N'', N'', N'', CAST(0x0000A6A9011775CC AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (1448, N'常识', 0, N'', N'comclass_50ed148e0b0641c692692eb1ac76e115', N'comclass_ac6c5f7023d147f4ab84e7cdc2c1452a', N'|End0||comclass_ac6c5f7023d147f4ab84e7cdc2c1452a|', N'', N'', N'ChangShi', N'', N'', N'', CAST(0x0000A6A901177BA8 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (1449, N'合作案例', 0, N'', N'comclass_05ce3fb57b2a40d78061a93f25979626', N'comclass_f7085d7f0b404968bf17d24d0e483b6b', N'|End0||comclass_f7085d7f0b404968bf17d24d0e483b6b|', N'', N'', N'HeZuoAnLi', N'', N'', N'', CAST(0x0000A6CF0117AE0C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (2451, N'共享文件柜', 0, N'', N'comclass_848519f43f1f4f33b82bd5de9eb162bf', N'0', N'|End0|', N'', N'', N'GongXiangWenJianGui', N'', N'', N'', CAST(0x0000A7200110DF78 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (2452, N'业务知识', 0, N'', N'comclass_585d1aaab03a4d8fa71ae89c7a0b9561', N'comclass_848519f43f1f4f33b82bd5de9eb162bf', N'|End0||comclass_848519f43f1f4f33b82bd5de9eb162bf|', N'', N'', N'YeWuZhiShi', N'', N'', N'', CAST(0x0000A72001112F28 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (2453, N'励志篇章', 0, N'', N'comclass_427e5854fa5f42bf83ed3e537e4e0d2b', N'comclass_848519f43f1f4f33b82bd5de9eb162bf', N'|End0||comclass_848519f43f1f4f33b82bd5de9eb162bf|', N'', N'', N'LiZhiPianZhang', N'', N'', N'', CAST(0x0000A7200111582C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (2454, N'合同样本', 0, N'', N'comclass_58612f4f425c4846a77964dbdcc013a6', N'comclass_848519f43f1f4f33b82bd5de9eb162bf', N'|End0||comclass_848519f43f1f4f33b82bd5de9eb162bf|', N'', N'', N'HeTongYangBen', N'', N'', N'', CAST(0x0000A720011170C8 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (2455, N'装机必备', 0, N'', N'comclass_ac0ac692141146b8b1d8cfebcc06a820', N'comclass_848519f43f1f4f33b82bd5de9eb162bf', N'|End0||comclass_848519f43f1f4f33b82bd5de9eb162bf|', N'', N'', N'ZhuangJiBiBei', N'', N'', N'', CAST(0x0000A7200111BCF4 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3467, N'客服电话', 0, N'', N'comclass_213fdea21baa44b99c98ee3045fb0af9', N'0', N'|End0|', N'等号前面是婚宴客服电话，后面是婚庆客服电话必须按这个格式书写，如：110-300=119-888；这里不做任何的输入内容；只是提示说明，不要在这里操作；', N'KeFuPhone', N'KeFuDianHua', N'110-300=119-888', N'', N'', CAST(0x0000A73600C3576C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3468, N'关于我们', 0, N'', N'comclass_0d7d670b34b34f528bb86a38882c5b16', N'0', N'|End0|', N'这里只是输入说明：要输入''关于我们''的内容在下面的输入框；不要在操作；', N'aboutus', N'GuanYuWoMen', N'SLN函数实现固定资产按直线法计提折旧_百度经验', N'', N'', CAST(0x0000A73600C3945C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3469, N'余额说明', 0, N'', N'comclass_e94f3f72840a4ddbaae943d7a07e355a', N'0', N'|End0|', N'这里只是输入说明：要输入''余额说明''的内容在下面的输入框；不要在这里操作；', N'YgIntro', N'YuE', N'SLN又称化学计量比铌酸锂，是20世纪90年代初发展起来的新一代亚微粒给药系统，SLN 函', N'', N'', CAST(0x0000A73600C3C33C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3471, N'添加客户资料', 0, N'', N'comclass_d48fa4a90a1944c2bd89a3124ec278b4', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'+++++需要进行token验证+|只有token为get,其它为post++

CltName:客户名；
Phone：手机号
FeastType:类型 0是婚宴，1是婚庆
Address：区域，婚礼举办的位置
Area:宴会规模；
Budget：预算；
Scale：婚期；
Remark：备注；
Greos：咨询酒店（选填）


', N'/amapi/SimplePlanCmd.aspx?cmd=AddComNews&token=', N'', N'', N'', N'', CAST(0x0000A73C01172E50 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3472, N'添加转账账号', 0, N'', N'comclass_ac2c5bbb19d94dcc96db4e29904034f4', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'+++++需要进行token验证+|只有token为get,其它为post+++

-------------添加支付宝----------------------------
UserName：真实名字
Bank：支付宝账号；(代号为0)时
Type:类型
----------------添加银行卡---------------------------
UserName：真实名字
Bank：银行卡账号；
Type：类型
Approve：支行信息


', N'/amapi/SimplePlanCmd.aspx?cmd=AddRank&token=', N'', N'支付宝   "0"

中国银行 "1"

建设银行 "2"

农业银行 "3"

工商银行 "4"

招商银行 "5"

交通银行 "6"

民生银行 "7"
 
中信银行 "8" 

光大银行 "9"

浦发银行 "10"
 
邮政银行 "11"', N'', N'', CAST(0x0000A73C0117D5E4 AS DateTime))
GO
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3474, N'修改昵称/头像', 0, N'', N'comclass_2e92dccb37ec4e3eb5b302b375a965c8', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'+++++需要进行token验证+|只有token为get,其它为post+++++

Nike：修改昵称的值；
Photo：图片的URL;

Type：类型
（1是修改昵称，2是更换头像，[如：只修改昵称.传："1",两个都修改.传："12"]）', N'/amapi/SimplePlanCmd.aspx?cmd=UpNike&token=', N'', N'', N'', N'', CAST(0x0000A73C011A7B3C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3475, N'提现', 0, N'', N'comclass_acad41cdbde440668081cd4e934c0002', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'+++++需要进行token验证+|只有token为get,其它为post+++++

Number：提现金额；Exnum：提现账号', N'/amapi/SimplePlanCmd.aspx?cmd=Extra&token=', N'', N'', N'', N'', CAST(0x0000A73C011BE42C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3476, N'申请跟单', 0, N'', N'comclass_014836acdcda4760bab50534f6342bdf', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'+++++需要进行token验证+|只有token为get,其它为post+++++

Type：婚宴类型(0婚宴 1婚庆)；
Customer：顾客的sid', N'/amapi/SimplePlanCmd.aspx?cmd=UpDocument&token=', N'', N'', N'', N'', CAST(0x0000A73C011C3508 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3477, N'泛搜索酒店', 0, N'', N'comclass_c908402eb88f479994c84a648f18050b', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'+++++++++++方式Get++++++++++++++++++++++
传两个参数，第一个用来区分是按酒店名查还是按酒店地址查，第二个参数酒店名和地址，用英文半角逗号分开。
--- 0是按酒店名，1是酒店地址；
====按酒店名和地址查如："0，希尔顿大酒店"

添加参数实列（所有传参都这样）：
【参数在ByGrogLike后面加，如ByGrogLike0,希尔顿  0是按名字查，希尔顿是酒店名，用英文半角的逗号分开】', N'/amapi/DListCenter.aspx?AppType=fotelshoplist___ByGrogLike&outdata=jsonlist', N'', N'++++++++获取到的数据+++++++++++++++++++++
返回的字段：GrogName：酒店名；Img：图片；Grod：酒店sid；Price：价格；Scale：规模；
-----------------------------------------------
"GrogName":"希尔顿大酒店",
"Img":"/upath/2017/3/14/104625397650238ufi.jpg",
"Grod":"xoBmD7yksf%2bkLwTJ2VEHRfcj4%2bl8rmA1m%2fvma22HL20VGQ0TtHzYqRMgyDlcULRK",
"Price":"200",
"Scale":"1000",
"rank":"4",
"site":"浦东新区"', N'', N'', CAST(0x0000A73C011D0924 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3478, N'获取餐厅信息', 0, N'', N'comclass_c48e1f036ba344e99c40f452082c6845', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'+++++++++++方式Get++++++++++++++++++++++

只传酒店的sid
', N'/amapi/DListCenter.aspx?AppType=ambiencelist___ByAmbience&outdata=jsonlist ', N'', N'++++++++返回的字段+++++++++++++++++++++
Photo：图片；CantName：餐厅名，Scale：餐厅规模；Price：价格；Intro：简介；Height：高度；

-------------------返回的数据---------------------
"Photo":["upath\\2017\\3\\14\\105519858340497ufi.jpg","upath\\2017\\3\\14\\105529879830054ufi.jpg"],
"CantName":"无多利亚",
"Scale":"300",
"Price":"0",
"Intro":"耗子",
"Height":"30米"
', N'', N'', CAST(0x0000A73C011F19A8 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3479, N'获取账单', 0, N'', N'comclass_2b84df374e2a442685bec1e6f1809579', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'+++++++++++方式Get++++++++++++++++++++++
只传用户的sid；', N'/amapi/DListCenter.aspx?AppType=showlist__ByBillid&outdata=jsonlist', N'', N'++++++++返回的字段+++++++++++++++++++++
CantName：提现金额；(ExType：提现状态：0 审核中，1 成功，2 失败；)Yun：佣金；

-------------------返回的数据---------------------
"CantName":"222",
"ExType":"1",
"Yun":"1000"', N'', N'', CAST(0x0000A73C01208ACC AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3480, N'获取客户列表信息', 0, N'', N'comclass_26fa7dda148c408793057d2636a454ad', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'+++++++++++方式Get++++++++++++++++++++++

=传3个参数，第一个传接收的类型（0婚宴，1婚庆），第二个传状态，第三个传用户的sid,', N'/amapi/DListCenter.aspx?AppType=groglist__ByNltd&outdata=jsonlist', N'', N'++++++++返回的字段+++++++++++++++++++++
UserName//客户名；Phone//手机号；Grod//sid ；NewsType//信息类型 0婚宴 1婚庆；Area//区域（举办婚礼的地方）
YanHui//宴会规模；Budget//预算；HqTime//婚期；Remark//客户备注；
RemarkTime//状态；  ["待跟进|0","跟进中|1","已成单|2",,"失效|3"]   
Hotel//咨询酒店（选填）
++++++++++获取的信息+++++++++++++++++++
"UserName":"老王",
"Phone":"******0",
"NewsType":"0",
"Area":"浦东新区",
"YanHui":"100桌",
"Budget":"100",
"HqTime":"2017/1/1 0:00:00",
"Remark":"麻子的儿子和张三的女儿的孙女",
"RemarkTime":"0",
"Hotel":"希尔顿"


', N'', N'', CAST(0x0000A73C01215330 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3481, N'关于我们,余额说明', 0, N'', N'comclass_927e35f683fe4ba7bf677649bd214b1d', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'
关于我们，URL:  /amapi/CAPool/ComHtmlPage.aspx?pagename=aboutus&tidx=1

余额说明，URL:  /amapi/CAPool/ComHtmlPage.aspx?pagename=YgIntro&tidx=1', N'', N'KeFuGuanYuWoMenYuEShuoMing', N'', N'', N'', CAST(0x0000A73C01258CD4 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3482, N'个人资料', 0, N'', N'comclass_ecf37bfd326e46c2b7107e7a7464fce2', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'+++++++++++方式Get++++++++++++++++++++++

只传用户的sid;
', N'/amapi/DListCenter.aspx?AppType=introlist__ByIntro&outdata=jsonlist', N'', N'++++++++返回的字段+++++++++++++++++++++
phone：手机号(账号)；photo：个人头像；nike：昵称；role：角色（职务）
+++++++++++++++返回的数据++++++++++
"phone":"15800327113",
"photo":"/upath/2017/3/14/131257948622540ufi.jpg",
"nike":"000",
"role":"systemrole_80ef7a69181940de8d92c059adf2f5ca"', N'', N'', CAST(0x0000A73C01260DBC AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3483, N'个人账号', 0, N'', N'comclass_0527123c67394b608467c2130e86e333', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'+++++++++++方式Get++++++++++++++++++++++

只传用户的sid;', N'/amapi/DListCenter.aspx?AppType=numberlist__ByNumber&outdata=jsonlist', N'', N'++++++++返回的字段+++++++++++++++++++++
name：用户的真实名字；number：账号；bansid：账号的sid
banktype:账号类型；zhih：支行信息
+++++++++++++++返回的数据++++++++++
"name":"66",
"number":"222"
banktype："0"   代表支付宝

支付宝   "0"

中国银行 "1"

建设银行 "2"

农业银行 "3"

工商银行 "4"

招商银行 "5"

交通银行 "6"

民生银行 "7"
 
中信银行 "8" 

光大银行 "9"

浦发银行 "10"
 
邮政银行 "11"', N'', N'', CAST(0x0000A73C0137FE50 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3484, N'登录', 0, N'', N'comclass_55705d8d34a14f5eba5cdc9f26a14417', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'-------------传值方式：Post-----------------

参数：{"uid":"kedll","pwd":"123456","dwc":"123"}

uid:账号；pwd：密码；dwc：设备编号；', N'CAPool/ClientLogin.aspx', N'DengLu', N'', N'', N'', CAST(0x0000A73C01406BBC AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3485, N'注册', 0, N'', N'comclass_fe6a278070674573a41d85dfdfd67ba2', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'--------------传值方式：Post------------------
参数：data
{"pwd":"123","dwc":"123","mbp":"158","tfn":"张三","sex":"希尔顿"}
pwd：密码；dwc：设备编号；mbp：手机号；tjh：推荐号；
tfn:真实名字；sex:酒店', N'/CAPool/ClientRegister.aspx', N'', N'', N'', N'', CAST(0x0000A73C0141EC1C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3486, N'验证码', 0, N'', N'comclass_3c3a6a67757847de8279dbd8c0eef344', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'-------------传值方式：Get--------------
参数：1.mphone：手机号码；2.cmsg：（JZ:提示$vcode$信息；RL:模板ID）；3.job：（reg:用户注册；fpw:找回密码）


', N'CAPool/VerificationSMS.aspx', N'', N'', N'', N'', CAST(0x0000A73C014287F8 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3487, N'统计数据', 0, N'', N'comclass_d9dd7c82364446288d7bff49eabf6bc7', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'+++++++++++Get的方式++++++++++++++
只传用户的sid;
++++++返回的字段++++++++++++++++++++
cltname：顾客名字;      phone：手机;
newstype：信息类型(0婚宴 1婚庆);
address：区域;             yanhui：宴会规模;
readvol：预算;              hptime：婚期;
fbtype：发布状态（0未发布,1已发布）;
remark：备注;    
cdtype：是否成单（0未成单,1已成单）
yxtype：是否有效（0有效,1无效）


', N'/amapi/DListCenter.aspx?AppType=statislist__ByStaClt&outdata=jsonlist', N'', N'++++++++++返回数据+++++++++++++++++++++++
"cltname":"老王","phone":"110","newstype":"0","address":"浦东新区","yanhui":"100桌","readvol":"100","hptime":"2017/1/1 0:00:00","fbtype":"1","remark":"麻子的儿子和张三的女儿的孙女","cdtype":"0","yxtype":"0"', N'', N'', CAST(0x0000A73E00C26154 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (3488, N'修改密码', 0, N'', N'comclass_ed396804bd464f25bfeea4e95b6dffba', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'++++++++++Get/Post+++++++++++++++
token认证
field_11 :修改的密码；  
', N'/CAPool/UpdClienter.aspx', N'XiuGaiMiMa', N'', N'', N'', CAST(0x0000A73E013BA0C8 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4488, N'酒店分页查询', 0, N'', N'comclass_10b60946b262412e9c5fed261bba3d6b', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'参数：地级市名字', N'/amapi/DListCenter.aspx?AppType=jiudianlist__ByAddress&outdata=jsonlist ', N'', N'++++++++获取到的数据+++++++++++++++++++++
返回的字段：GrogName：酒店名；Img：图片；Grod：酒店sid；Price：价格；Scale：规模；
-----------------------------------------------
"GrogName":"希尔顿大酒店",
"Img":"/upath/2017/3/14/104625397650238ufi.jpg",
"Grod":"xoBmD7yksf%2bkLwTJ2VEHRfcj4%2bl8rmA1m%2fvma22HL20VGQ0TtHzYqRMgyDlcULRK",
"Price":"200",
"Scale":"1000",
"rank":"4",
"site":"浦东新区"', N'', N'', CAST(0x0000A74100AC0134 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4489, N'账户余额', 0, N'', N'comclass_543d76073eaf4a50b918d8a7a5305d39', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'返回的字段：money:金额；', N'/amapi/DListCenter.aspx?AppType=grossmlist__ByGross&outdata=jsonlist', N'', N'+++++++++++++++所需数据+++++++++++++++++++++
money:938;
', N'', N'', CAST(0x0000A74100AC34C4 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4490, N'【001】客户端全局配置文件', 46, N'', N'comclass_45a725e7079147efb37b23dec7381a4b', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'常用全局配置信息：
========================== GET =======================
静态文件，无需传递参数。
=================== RETURN =================
<?xml version="1.0" encoding="UTF-8"?>
<root>
  <result title="上海卡斗" code="200" msg="">
    <item title="WPhone" intr="终端版本控制" date="1.1" dev="1.1" mindev="1.1">https://itunes.apple.com/cn/app/shi-cai-qing-bao/id864095149?mt=8</item>
    <item title="Android" intr="终端版本控制" date="1.0" dev="1.0" mindev="1.0">http://121.41.80.13/soft/DishesBrother.apk</item>
    <item title="iPhone" intr="终端版本控制" date="1.0" dev="1.0" mindev="1.0">https://itunes.apple.com/cn/app/shi-cai-qing-bao/id864095149?mt=8</item>
    <item title="AdInf" intr="广告信息" date="16010101080000">xml/AdInf/index.xml</item>
    <item title="AreaLib" intr="行政地区库" date="20160330161848">xml/AreaLib/index.xml</item>
    <item title="WXAuthUrl" intr="微信JS授权URL">https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx4e0b6cab407fda7f&amp;redirect_uri=http%3a%2f%2fwww.gxedu.me%2fCAPool%2fWeiXinApi.aspx&amp;response_type=code&amp;scope=snsapi_userinfo&amp;state=STATE#wechat_redirect</item>
    <item title="BootAnimation" intr="开机动画" date="821207" fzip="xml\BootAnimation.zip">[]</item>
  </result>
</root>
============================================
', N'/xml/version.xml', N'', N'', N'', N'', CAST(0x0000A74E01433C70 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4491, N'【002】跟据令牌或SID取客户信息', 44, N'', N'comclass_85693d31ca9642338d36f8f66c908a2c', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'=========GET=============================================
必选项--token--（令牌、客户sid）
=========POST============================================
=========Model===========================================
<?xml version="1.0"?>
<root title="取用户信息">
  <result msg="" code="200" time="12.7158毫秒">
    <userinf
        rid="2"
        sid=""
        userid=""
        binddata="第三方ID"
        headimg=""
        sexid="0"
        birthday=""
        nickname=""
        mobile=""
        email=""
        jwdu=""
        lstatu="在线状态0，1"
        pstatu="审核状态0，1"
        aboutme=""
        cost="佣金量"
        token=""
        age="0"
        txpwd="提现密码"
        integral="用户积分"
        recommended="1"
        cpnaddr="公司地址"
        balance="账户余额"
        idcard="身份证号"
        rolename=""
        citysid=""
        cityname="中国-&gt;北京市"
        addrsid=""
        addressname=""
        certsfc=""
        certs=""
    />
  </result>
</root>', N'AMAPI/SimplePlanCmd.aspx?cmd=GetUserInf', N'', N'', N'', N'', CAST(0x0000A74E014360C4 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4492, N'【003】获取短信验证码', 41, N'', N'comclass_9cf90e63722a449fae2ba426ee007ac8', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'=========GET=============================================
必选项--mphone=15800327113--（手机号码）
必选项--job=reg--（reg:用户注册；fpw:找回密码）
=========POST============================================
=========Model===========================================
<?xml version="1.0"?>
<root title="用户上传接口A">
  <result DeviceToken="admin" AppLockID="tmp_aa84" msg="" code="200">2ee7c8a</result>
</root>
', N'/CAPool/VerificationSMS.aspx', N'', N'', N'', N'', CAST(0x0000A74E01438770 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4493, N'【004】用经纬度取城市SID', 38, N'', N'comclass_08bf399ad4ed419d932825c71e2f7944', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'=========GET=============================================
必选项--wd=31.239571--（纬度）
必选项--jd=121.473803--（经度）
=========POST============================================
=========Model===========================================
<?xml version="1.0"?>
<root title="根据经纬度取城市">
  <result msg="上海市" code="200" time="136.6906毫秒">
    <inf sid="lwovPX6nGTq%2fOfd" name="上海市" />
  </result>
</root>', N'CAPool/GetCityByWJ.aspx', N'', N'', N'', N'', CAST(0x0000A74E01439A30 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4494, N'获取客户的备注内容', 0, N'', N'comclass_12fb8739b1324283929b77630561895a', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'客户的sid;', N'/amapi/DListCenter.aspx?AppType=remakelist___ByText&outdata=jsonlist', N'', N'返回的字段：usname：备注人，cltname：客户名；text：备注内容；time：时间；', N'', N'', CAST(0x0000A74F014F3AC0 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4495, N'搜索客户', 0, N'', N'comclass_790599a72cee4d21a0903b82948a1944', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'=传3个参数，第一个传接收的类型（0婚宴，1婚庆），第2个搜索的客户名，第3个传用户的sid;', N'/amapi/DListCenter.aspx?AppType=selecltlist__BySele&outdata=jsonlist ', N'', N'====================返回的字段================
UserName//客户名；Phone//手机号；Grod//sid ；NewsType//信息类型 0婚宴 1婚庆；Area//区域（举办婚礼的地方）
YanHui//宴会规模；Budget//预算；HqTime//婚期；Remark//客户备注；
RemarkTime//状态；  ["待跟进|0","跟进中|1","已成单|2","失效|3"]   
Hotel//咨询酒店（选填）
==================所需数据==================
"UserName":"老王","Phone":"******0","NewsType":"0","Area":"浦东新区","YanHui":"100桌","Budget":"100",
"HqTime":"2017/1/1 0:00:00","Remark":"麻子的儿子和张三的女儿的孙女","RemarkTime":"0",
"Hotel":"希尔顿"
', N'', N'', CAST(0x0000A74F01509B7C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4496, N'客户详细资料', 0, N'', N'comclass_9ff483b4a85c43a1a0bf2cbf1be4f921', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==第一个是类型，第二个是用户的sid；第三是客户的sid；', N'/amapi/DListCenter.aspx?AppType=clitnerlist__BySelclt&outdata=jsonlist', N'', N'UserName//客户名；Phone//手机号；Grod//sid ；NewsType//信息类型 0婚宴 1婚庆；Area//区域（举办婚礼的地方）
YanHui//宴会规模；Budget//预算；HqTime//婚期；Remark//客户备注；
RemarkTime//状态；  ["待跟进|0","跟进中|1","已成单|2","失效|3"]   
Hotel//咨询酒店（选填）
', N'', N'', CAST(0x0000A74F0150D290 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4497, N'统计数据列表', 0, N'', N'comclass_a906a40167de4ea3809982f4c7da481a', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'传参数==1.页面刚加载时按状态查询：第一个传状态，第二个传用户的sid;
2.按时间查询得到的数据汇总按状态：第一个传开始时间；第二个传结束时间；第三个传状态，第四个传用户的sid；', N'/amapi/DListCenter.aspx?AppType=statlist__Bystat&outdata=jsonlist', N'', N'', N'', N'', CAST(0x0000A74F01511688 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4498, N'统计数据', 0, N'', N'comclass_2db2790820854743ab95b90a66f77453', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'==传参数：用Tmapv (不用再By开头的字段里传，)
1.页面刚加载时：只传用户的sid；2.按时间查询得到的数据汇总：第一个传开始时间；第二个传结束时间；第三个传用户的sid;
', N'/amapi/SimplePlanCmd.aspx?cmd=Test&outdata=jsonlist', N'', N'==返回的字段== sum：总数；valid：有效；vain：无效；make：成交；', N'', N'', CAST(0x0000A74F0151605C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4499, N'轮播图', 0, N'', N'comclass_2a0969543bd94ed5be0771ae5c13f390', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'====参数---不用传任何参数；', N'/amapi/DListCenter.aspx?AppType=fenyelist__&outdata=jsonlist', N'', N'返回的字段：GrogName：酒店名；Img：图片；Grod：酒店sid；Price：价格；Scale：规模；rank;星级；site:区域；
adress：详细地址', N'', N'', CAST(0x0000A74F0151A1FC AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4500, N'按钮式的搜索酒店；', 0, N'', N'comclass_3b4b32c983354f87a60320a59f3ffce1', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'参数== 只传市级名的sid；', N'/amapi/DListCenter.aspx?AppType=gaddresslist__ByGadd&outdata=jsonlist', N'', N'', N'', N'', CAST(0x0000A74F0151C77C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4501, N'版本更新', 0, N'', N'comclass_68df78f727da4b66919baa816ce36c51', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'', N'http://wedding.kedll.com/xml/Version.xml', N'', N'', N'', N'', CAST(0x0000A74F0151EAA4 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4502, N'验证备注权限', 0, N'', N'comclass_6baddc84128f453dae6d73b3c05408cd', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'Cltsid:客户的sid；', N'/amapi/SimplePlanCmd.aspx?cmd=Check&token=', N'', N'', N'', N'', CAST(0x0000A74F01521BDC AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4503, N'获取省区名', 0, N'', N'comclass_b9fe64a9549745b583cc5316f64e3c2f', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'不用token认证 不传参', N'/amapi/SimplePlanCmd.aspx?cmd=Province', N'', N'返回的字段：proname：省级名；prosid：省级的sid；', N'', N'', CAST(0x0000A74F01524E40 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4504, N'获取市区名', 0, N'', N'comclass_c72271c9995d4dbc9a7e63e986535992', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'不用token认证
参数：Province：省级的sid;', N'/amapi/SimplePlanCmd.aspx?cmd=Secity', N'', N'返回的字段：cityname：市级名；citysid：市级的sid；', N'', N'', CAST(0x0000A74F0152E7C4 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4505, N'修改状态', 0, N'', N'comclass_08f6bf68a53243f09653e79d7f2beb44', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'Cltsid:客户的sid； Type：状态，    0是成单申请  1无效申请', N'/amapi/SimplePlanCmd.aspx?cmd=Uptype&token=', N'', N'', N'', N'', CAST(0x0000A74F01538850 AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4506, N'删除账号', 0, N'', N'comclass_76a63cdc5cfc4303935791aeaf07462f', N'comclass_ac53f4575abb4414b8f1bee351b1f754', N'|End0||comclass_ac53f4575abb4414b8f1bee351b1f754|', N'参数==Banksid：账号的sid；', N'/amapi/SimplePlanCmd.aspx?cmd=Delbank&token=', N'', N'', N'', N'', CAST(0x0000A74F0153AA4C AS DateTime))
INSERT [dbo].[ComClass] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [field_9], [field_10], [field_11], [field_12], [CreateDate]) VALUES (4507, N'活动部', 0, N'', N'comclass_07010ce6772d44a7abb9663f0eaac463', N'comclass_c28775c4915640d2991ca7603fc0bc05', N'|End0||comclass_c28775c4915640d2991ca7603fc0bc05|', N'', N'', N'', N'', N'', N'', CAST(0x0000A75B00C4FC20 AS DateTime))
SET IDENTITY_INSERT [dbo].[ComClass] OFF

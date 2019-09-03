USE [Wedding]
GO
/****** Object:  Table [dbo].[ApiUserEvent]    Script Date: 2019/7/24 16:01:31 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[ApiUserEvent](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](200) NULL,
	[field_2] [bigint] NULL,
	[field_3] [bigint] NULL,
	[field_4] [varchar](100) NULL,
	[field_5] [bigint] NULL,
	[field_6] [bigint] NULL,
	[field_7] [datetime] NULL,
	[field_8] [varchar](2000) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_ApiUserEvent] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[ApiUserEvent] ON 

INSERT [dbo].[ApiUserEvent] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [CreateDate]) VALUES (27, N'/amapi/dlistcenter.aspx', 955688, 441813026, N'apiuserevent_66074972bf804e08a9bdebadb6cb3425', 338166269, 147876772, CAST(0x0000AA94010825E0 AS DateTime), N'输出数据超统范【2019/6/8 17:07:54】
输出数据超统范【2019/7/20 19:59:10】
', CAST(0x0000AA440030C9D8 AS DateTime))
INSERT [dbo].[ApiUserEvent] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [CreateDate]) VALUES (28, N'/applens/postdevicetoken.aspx', 11616, 368123, N'apiuserevent_d02ccee7e24e4df2bea727163bb251a8', 5511725, 2893016, CAST(0x0000AA9401081C80 AS DateTime), N'', CAST(0x0000AA440030C9D8 AS DateTime))
INSERT [dbo].[ApiUserEvent] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [CreateDate]) VALUES (29, N'/amapi/simpleplancmd.aspx', 336948, 26782674, N'apiuserevent_3a7a82a9990a4044b71e72dca3cfd05c', 310031953, 30411130, CAST(0x0000AA940107EDA0 AS DateTime), N'', CAST(0x0000AA44007BD464 AS DateTime))
INSERT [dbo].[ApiUserEvent] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [CreateDate]) VALUES (30, N'/capool/cltlogin.aspx', 908, 31842, N'apiuserevent_bdea2524fa324ee8971adad8a3346e99', 357911, 558987, CAST(0x0000AA9400FB4C6C AS DateTime), N'', CAST(0x0000AA4400A70760 AS DateTime))
INSERT [dbo].[ApiUserEvent] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [CreateDate]) VALUES (31, N'/amapi/capool/cltlogin.aspx', 545, 13834, N'apiuserevent_7b5d2b2c175d44b1a2d8d98d2bedc3ff', 147150, 395685, CAST(0x0000AA9400CE16FC AS DateTime), N'', CAST(0x0000AA4400BFD36C AS DateTime))
INSERT [dbo].[ApiUserEvent] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [CreateDate]) VALUES (32, N'/amapi/capool/verificationsms.aspx', 164, 19779, N'apiuserevent_c1d68e2397ab4f01ad25629961941547', 50326, 14406, CAST(0x0000AA9300FB2818 AS DateTime), N'', CAST(0x0000AA45010BC858 AS DateTime))
INSERT [dbo].[ApiUserEvent] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [CreateDate]) VALUES (33, N'/amapi/capool/clientreg.aspx', 8, 903, N'apiuserevent_632d80c934cc49068bac00eaa86982ce', 2740, 4167, CAST(0x0000AA8C010F881C AS DateTime), N'', CAST(0x0000AA45010BDB18 AS DateTime))
INSERT [dbo].[ApiUserEvent] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [CreateDate]) VALUES (34, N'/capool/clientreg.aspx', 108, 2347, N'apiuserevent_a269eb031360499eb65184b222e3945b', 55362, 21394, CAST(0x0000AA9300EF9598 AS DateTime), N'', CAST(0x0000AA480101448C AS DateTime))
INSERT [dbo].[ApiUserEvent] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [CreateDate]) VALUES (35, N'/amapi/capool/updclienter.aspx', 664, 15658, N'apiuserevent_4964e3b76ed24c4eae5bb53014b1a9f1', 280795, 46511, CAST(0x0000AA9400FB04F0 AS DateTime), N'', CAST(0x0000AA4F00BE43D0 AS DateTime))
INSERT [dbo].[ApiUserEvent] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [CreateDate]) VALUES (36, N'/capool/updclienter.aspx', 2, 88, N'apiuserevent_4e1c24ebcdfa4e36a70ba3561cb843d3', 826, 140, CAST(0x0000AA88011966AC AS DateTime), N'', CAST(0x0000AA6A00EC2DB8 AS DateTime))
INSERT [dbo].[ApiUserEvent] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [field_6], [field_7], [field_8], [CreateDate]) VALUES (37, N'/admin/app_addclt/hyrecordlist.aspx', 1, 368, N'apiuserevent_6668f3c122e94ae58e25549aac5eb6fa', 1382, 5303, CAST(0x0000AA9400DC3C14 AS DateTime), N'', CAST(0x0000AA9400DC3C14 AS DateTime))
SET IDENTITY_INSERT [dbo].[ApiUserEvent] OFF

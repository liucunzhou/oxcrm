USE [Wedding]
GO
/****** Object:  Table [dbo].[SystemRole]    Script Date: 2019/7/24 16:01:31 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[SystemRole](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [nvarchar](50) NULL,
	[field_2] [int] NULL,
	[field_3] [nvarchar](500) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](50) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_SystemRole] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[SystemRole] ON 

INSERT [dbo].[SystemRole] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [CreateDate]) VALUES (1, N'超级管理员', 4, N'超级管理员说明', N'systemrole_2e42c6d1c7404c5ba929f4b729bfe15d', N'syscfg_8a1aa38f645145a99ad1a4a07a40397b', CAST(0x00009C7F00000000 AS DateTime))
INSERT [dbo].[SystemRole] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [CreateDate]) VALUES (9, N'商户会员', 0, N'一般会员', N'systemrole_80ef7a69181940de8d92c059adf2f5ca', N'syscfg_63a4bac1c1564e848d74b8283e768a15', CAST(0x0000A6D90113283C AS DateTime))
INSERT [dbo].[SystemRole] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [CreateDate]) VALUES (1008, N'婚宴业务员', 0, N'婚宴类业务', N'systemrole_af63079d4a0b45ee9590bd31a2a09453', N'syscfg_63a4bac1c1564e848d74b8283e768a15', CAST(0x0000A72D018137A0 AS DateTime))
INSERT [dbo].[SystemRole] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [CreateDate]) VALUES (1009, N'婚庆业务员', 0, N'婚庆类业务', N'systemrole_91e33bf480ef45428310afd7a2353a2d', N'syscfg_63a4bac1c1564e848d74b8283e768a15', CAST(0x0000A72D01817940 AS DateTime))
INSERT [dbo].[SystemRole] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [CreateDate]) VALUES (1010, N'双向业务员', 0, N'双向业务员', N'systemrole_ce7e950a0a55448ebe03694bb6d7853c', N'syscfg_63a4bac1c1564e848d74b8283e768a15', CAST(0x0000A744015937C8 AS DateTime))
INSERT [dbo].[SystemRole] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [CreateDate]) VALUES (1011, N'部门管理员', 0, N'管理员说明', N'systemrole_06a097e16d9c4c339e9c0f6165c616c4', N'syscfg_63a4bac1c1564e848d74b8283e768a15', CAST(0x0000A7560188C1B4 AS DateTime))
INSERT [dbo].[SystemRole] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [CreateDate]) VALUES (1012, N'区域管理员', 0, N'区域管理员', N'systemrole_49db259796cf42dc9745746d87b7aadd', N'syscfg_63a4bac1c1564e848d74b8283e768a15', CAST(0x0000A81501477290 AS DateTime))
INSERT [dbo].[SystemRole] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [CreateDate]) VALUES (1013, N'后台管理员', 0, N'后台管理员', N'systemrole_1020fd7f804a45ed9ca4fbd743e95d87', N'syscfg_8a1aa38f645145a99ad1a4a07a40397b', CAST(0x0000A8CA010A8BF0 AS DateTime))
INSERT [dbo].[SystemRole] ([id], [field_1], [field_2], [field_3], [field_4], [field_5], [CreateDate]) VALUES (1014, N'普通员工', 0, N'普通员工', N'systemrole_c9c1969e39ec40ac9ee7fad2c2ac7e4e', N'syscfg_8a1aa38f645145a99ad1a4a07a40397b', CAST(0x0000A9E50187278C AS DateTime))
SET IDENTITY_INSERT [dbo].[SystemRole] OFF

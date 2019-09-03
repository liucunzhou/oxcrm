USE [Wedding]
GO
/****** Object:  Table [dbo].[OrganSt]    Script Date: 2019/7/24 16:01:31 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[OrganSt](
	[Number] [varchar](50) NULL,
	[AreaSid] [varchar](50) NULL,
	[Id] [int] IDENTITY(1,1) NOT NULL,
	[Title] [varchar](50) NULL,
	[Sid] [varchar](50) NULL,
	[PrtSid] [varchar](50) NULL,
	[Level] [int] NULL,
	[AdminSid] [varchar](50) NULL,
	[TitleTree] [varchar](2000) NULL,
	[SidTree] [varchar](2000) NULL,
	[CreateTime] [datetime] NULL,
 CONSTRAINT [PK_OrganSt] PRIMARY KEY CLUSTERED 
(
	[Id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[OrganSt] ON 

INSERT [dbo].[OrganSt] ([Number], [AreaSid], [Id], [Title], [Sid], [PrtSid], [Level], [AdminSid], [TitleTree], [SidTree], [CreateTime]) VALUES (N'1', N'gaddress_f1adbde5a3724a7fbed2bd1e1b711cac', 8, N'婚宴1部', N'ORGANST_9100854759f841e786b3d6703ae3099b', N'', 0, N'partner_2dbe9db157d54ab68917a3369a6aebc1', N'|婚宴1部|', N'|ORGANST_9100854759f841e786b3d6703ae3099b|', CAST(0x0000A81D00BB7FE8 AS DateTime))
INSERT [dbo].[OrganSt] ([Number], [AreaSid], [Id], [Title], [Sid], [PrtSid], [Level], [AdminSid], [TitleTree], [SidTree], [CreateTime]) VALUES (N'1', N'gaddress_f1adbde5a3724a7fbed2bd1e1b711cac', 10, N'婚庆1部', N'ORGANST_5df26c26394d43f58751727938770769', N'', 0, N'partner_2dbe9db157d54ab68917a3369a6aebc1', N'|婚庆1部|', N'|ORGANST_5df26c26394d43f58751727938770769|', CAST(0x0000A81F00E29EF3 AS DateTime))
INSERT [dbo].[OrganSt] ([Number], [AreaSid], [Id], [Title], [Sid], [PrtSid], [Level], [AdminSid], [TitleTree], [SidTree], [CreateTime]) VALUES (N'婚宴2部', N'gaddress_f1adbde5a3724a7fbed2bd1e1b711cac', 11, N'婚宴2部', N'ORGANST_e7aeb00ee7b8406288efccad2fa5fd33', N'', 0, N'partner_2dbe9db157d54ab68917a3369a6aebc1', N'|婚宴2部|', N'|ORGANST_e7aeb00ee7b8406288efccad2fa5fd33|', CAST(0x0000A821010B464C AS DateTime))
INSERT [dbo].[OrganSt] ([Number], [AreaSid], [Id], [Title], [Sid], [PrtSid], [Level], [AdminSid], [TitleTree], [SidTree], [CreateTime]) VALUES (N'婚宴3部', N'gaddress_f1adbde5a3724a7fbed2bd1e1b711cac', 12, N'', N'ORGANST_8653d5ae29124ded95855e801f436f03', N'', 0, N'partner_2dbe9db157d54ab68917a3369a6aebc1', N'||', N'|ORGANST_8653d5ae29124ded95855e801f436f03|', CAST(0x0000A82400E5F744 AS DateTime))
INSERT [dbo].[OrganSt] ([Number], [AreaSid], [Id], [Title], [Sid], [PrtSid], [Level], [AdminSid], [TitleTree], [SidTree], [CreateTime]) VALUES (N'广州部', N'gaddress_bd3a389e9bd54385b80be970eca817dd', 13, N'广州部', N'ORGANST_60ac1b862fd546a9b35af6485c41ea36', N'', 0, N'partner_2dbe9db157d54ab68917a3369a6aebc1', N'|广州部|', N'|ORGANST_60ac1b862fd546a9b35af6485c41ea36|', CAST(0x0000A82601175457 AS DateTime))
INSERT [dbo].[OrganSt] ([Number], [AreaSid], [Id], [Title], [Sid], [PrtSid], [Level], [AdminSid], [TitleTree], [SidTree], [CreateTime]) VALUES (N'1', N'gaddress_c26112b9384f4568b886bc1d352ccd43', 15, N'婚宴1', N'ORGANST_1271fc91da3b4d51a5ce89d78ff52660', N'', 0, N'partner_2dbe9db157d54ab68917a3369a6aebc1', N'|婚宴1|', N'|ORGANST_1271fc91da3b4d51a5ce89d78ff52660|', CAST(0x0000A82A00C160A5 AS DateTime))
INSERT [dbo].[OrganSt] ([Number], [AreaSid], [Id], [Title], [Sid], [PrtSid], [Level], [AdminSid], [TitleTree], [SidTree], [CreateTime]) VALUES (N'上海婚宴管理', N'gaddress_f1adbde5a3724a7fbed2bd1e1b711cac', 16, N'上海婚宴管理', N'ORGANST_37543a6b348d43cf86a66f7401e3413e', N'', 0, N'partner_2dbe9db157d54ab68917a3369a6aebc1', N'|上海婚宴管理|', N'|ORGANST_37543a6b348d43cf86a66f7401e3413e|', CAST(0x0000A85B00DE0A9C AS DateTime))
SET IDENTITY_INSERT [dbo].[OrganSt] OFF

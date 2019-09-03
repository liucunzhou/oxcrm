USE [Wedding]
GO
/****** Object:  Table [dbo].[KillIP]    Script Date: 2019/7/24 16:01:31 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[KillIP](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](15) NULL,
	[field_2] [nvarchar](50) NULL,
	[field_3] [int] NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_KillIP] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
SET IDENTITY_INSERT [dbo].[KillIP] ON 

INSERT [dbo].[KillIP] ([id], [field_1], [field_2], [field_3], [CreateDate]) VALUES (4, N'214.25.25.26', N'585', 0, CAST(0x0000A0E400B15670 AS DateTime))
INSERT [dbo].[KillIP] ([id], [field_1], [field_2], [field_3], [CreateDate]) VALUES (5, N'192.168.0.104', N'/admin/SysLogin.aspx', 2, CAST(0x0000A0E400F3E0D0 AS DateTime))
INSERT [dbo].[KillIP] ([id], [field_1], [field_2], [field_3], [CreateDate]) VALUES (6, N'aaa', N'bbb', 0, CAST(0x0000A1BB00FD0AE8 AS DateTime))
SET IDENTITY_INSERT [dbo].[KillIP] OFF

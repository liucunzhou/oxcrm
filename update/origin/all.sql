USE [Wedding]
GO
/****** Object:  Table [dbo].[Addclt]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[Addclt](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](100) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](50) NULL,
	[field_6] [int] NULL,
	[field_7] [varchar](100) NULL,
	[field_8] [varchar](50) NULL,
	[field_9] [float] NULL,
	[field_10] [varchar](100) NULL,
	[field_11] [varchar](100) NULL,
	[field_12] [int] NULL,
	[field_13] [varchar](1000) NULL,
	[field_14] [int] NULL,
	[field_15] [varchar](100) NULL,
	[field_16] [varchar](100) NULL,
	[field_17] [varchar](200) NULL,
	[field_18] [datetime] NULL,
	[field_19] [datetime] NULL,
	[field_20] [varchar](500) NULL,
	[field_21] [varchar](100) NULL,
	[field_22] [varchar](100) NULL,
	[field_23] [varchar](50) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_Addclt] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY],
 CONSTRAINT [IX_Addclt_F4] UNIQUE NONCLUSTERED 
(
	[field_4] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[AdminMenu]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[AdminMenu](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](50) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](50) NULL,
	[field_6] [varchar](500) NULL,
	[field_7] [varchar](500) NULL,
	[field_8] [varchar](100) NULL,
	[field_9] [varchar](800) NULL,
	[field_10] [varchar](800) NULL,
	[field_11] [varchar](1000) NULL,
	[field_12] [int] NULL,
	[field_13] [text] NULL,
	[field_14] [int] NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_AdminMemu] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[AFULicenseID]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[AFULicenseID](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](50) NULL,
	[field_2] [varchar](50) NULL,
	[field_3] [varchar](200) NULL,
	[field_4] [int] NULL,
	[field_5] [int] NULL,
	[field_6] [varchar](100) NULL,
	[field_7] [varchar](100) NULL,
	[field_8] [varchar](100) NULL,
	[field_9] [int] NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_AFULicenseID] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[ApiGenNat]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[ApiGenNat](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](100) NULL,
	[field_4] [varchar](100) NULL,
	[field_5] [varchar](100) NULL,
	[field_6] [varchar](50) NULL,
	[field_7] [varchar](100) NULL,
	[field_8] [varchar](50) NULL,
	[field_9] [varchar](100) NULL,
	[field_10] [varchar](max) NULL,
	[field_11] [varchar](max) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_ApiGenNat] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[ApiUserEvent]    Script Date: 2019/7/24 15:55:49 ******/
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
/****** Object:  Table [dbo].[AppLog]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[AppLog](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](50) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](100) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](3000) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_AppLog] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[Apply]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[Apply](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](2000) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](100) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [int] NULL,
	[field_6] [int] NULL,
	[field_7] [int] NULL,
	[field_8] [int] NULL,
	[field_9] [int] NULL,
	[field_10] [int] NULL,
	[field_11] [varchar](100) NULL,
	[field_12] [varchar](100) NULL,
	[field_13] [datetime] NULL,
	[field_14] [varchar](300) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_Apply] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY],
 CONSTRAINT [IX_Apply_F4] UNIQUE NONCLUSTERED 
(
	[field_4] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[Binding]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[Binding](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](200) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](200) NULL,
	[field_4] [varchar](100) NULL,
	[field_5] [varchar](200) NULL,
	[field_6] [varchar](200) NULL,
	[field_7] [int] NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_Binding] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[Certificate]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[Certificate](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](50) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](100) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](50) NULL,
	[field_6] [varchar](50) NULL,
	[field_7] [varchar](200) NULL,
	[field_8] [int] NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_Certificate] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[CGMessage]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[CGMessage](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](2000) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](50) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](50) NULL,
	[field_6] [int] NULL,
	[field_7] [varchar](50) NULL,
	[field_8] [varchar](100) NULL,
	[field_9] [varchar](50) NULL,
	[field_10] [varchar](100) NULL,
	[field_11] [int] NULL,
	[field_12] [datetime] NULL,
	[field_13] [int] NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_CGMessage] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[Clienter]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[Clienter](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](200) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](50) NULL,
	[field_6] [int] NULL,
	[field_7] [int] NULL,
	[field_8] [int] NULL,
	[field_9] [datetime] NULL,
	[field_10] [datetime] NULL,
	[field_11] [varchar](50) NULL,
	[field_12] [varchar](500) NULL,
	[field_13] [varchar](100) NULL,
	[field_14] [varchar](100) NULL,
	[field_15] [varchar](50) NULL,
	[field_16] [int] NULL,
	[field_17] [varchar](500) NULL,
	[field_18] [varchar](50) NULL,
	[field_19] [varchar](100) NULL,
	[field_20] [varchar](1000) NULL,
	[field_21] [varchar](100) NULL,
	[field_22] [datetime] NULL,
	[field_23] [varchar](50) NULL,
	[field_24] [float] NULL,
	[field_25] [int] NULL,
	[field_26] [varchar](100) NULL,
	[field_27] [varchar](1000) NULL,
	[field_28] [varchar](500) NULL,
	[field_29] [varchar](50) NULL,
	[field_30] [varchar](200) NULL,
	[field_31] [varchar](200) NULL,
	[field_32] [varchar](500) NULL,
	[field_33] [int] NULL,
	[field_34] [int] NULL,
	[field_35] [int] NULL,
	[field_36] [varchar](200) NULL,
	[field_37] [varchar](100) NULL,
	[field_38] [float] NULL,
	[field_39] [int] NULL,
	[field_40] [varchar](50) NULL,
	[field_41] [varchar](50) NULL,
	[field_42] [varchar](200) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_Clienter] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY],
 CONSTRAINT [IX_Clienter_F4] UNIQUE NONCLUSTERED 
(
	[field_4] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[ComClass]    Script Date: 2019/7/24 15:55:49 ******/
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
/****** Object:  Table [dbo].[DataBaseEvt]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[DataBaseEvt](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](500) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](20) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](100) NULL,
	[field_6] [varchar](100) NULL,
	[field_7] [varchar](100) NULL,
	[field_8] [varchar](100) NULL,
	[field_9] [varchar](100) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_DataBaseEvt] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[DeviceToken]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[DeviceToken](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](50) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](100) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](50) NULL,
	[field_6] [varchar](500) NULL,
	[field_7] [varchar](200) NULL,
	[field_8] [varchar](50) NULL,
	[field_9] [datetime] NULL,
	[field_10] [varchar](50) NULL,
	[field_11] [varchar](100) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_DeviceToken] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[FileLib]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[FileLib](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [nvarchar](100) NULL,
	[field_2] [nvarchar](100) NULL,
	[field_3] [nvarchar](50) NULL,
	[field_4] [nvarchar](50) NULL,
	[field_5] [nvarchar](50) NULL,
	[field_6] [varchar](50) NULL,
	[field_7] [nvarchar](100) NULL,
	[field_8] [nvarchar](2000) NULL,
	[field_9] [nvarchar](225) NULL,
	[field_10] [int] NULL,
	[field_11] [varchar](100) NULL,
	[field_12] [varchar](100) NULL,
	[field_13] [varchar](50) NULL,
	[field_14] [int] NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_FileLib] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[GAddress]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[GAddress](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](50) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](50) NULL,
	[field_6] [varchar](100) NULL,
	[field_7] [int] NULL,
	[field_8] [varchar](500) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_GAddress] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[Gorgres]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[Gorgres](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](100) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](50) NULL,
	[field_6] [float] NULL,
	[field_7] [float] NULL,
	[field_8] [varchar](200) NULL,
	[field_9] [float] NULL,
	[field_10] [float] NULL,
	[field_11] [float] NULL,
	[field_12] [int] NULL,
	[field_13] [varchar](50) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_Gorgres] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[Grogshop]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[Grogshop](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](100) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [float] NULL,
	[field_6] [float] NULL,
	[field_7] [int] NULL,
	[field_8] [varchar](200) NULL,
	[field_9] [varchar](100) NULL,
	[field_10] [varchar](100) NULL,
	[field_11] [varchar](100) NULL,
	[field_12] [float] NULL,
	[field_13] [float] NULL,
	[field_14] [int] NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_Grogshop] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[InSdMsg]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[InSdMsg](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](100) NULL,
	[field_4] [varchar](100) NULL,
	[field_5] [varchar](100) NULL,
	[field_6] [varchar](1000) NULL,
	[field_7] [varchar](100) NULL,
	[field_8] [varchar](500) NULL,
	[field_9] [varchar](100) NULL,
	[field_10] [datetime] NULL,
	[field_11] [varchar](200) NULL,
	[field_12] [int] NULL,
	[field_13] [int] NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_InSdMsg] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY],
UNIQUE NONCLUSTERED 
(
	[field_4] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[ItnMail]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[ItnMail](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [varchar](50) NULL,
	[field_3] [varchar](100) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](100) NULL,
	[field_6] [int] NULL,
	[field_7] [int] NULL,
	[field_8] [int] NULL,
	[field_9] [varchar](50) NULL,
	[field_10] [varchar](50) NULL,
	[field_11] [datetime] NULL,
	[field_12] [text] NULL,
	[field_13] [varchar](50) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_ItnMail] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[KillIP]    Script Date: 2019/7/24 15:55:49 ******/
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
/****** Object:  Table [dbo].[KillIPTemp]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[KillIPTemp](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](15) NULL,
	[field_2] [nvarchar](50) NULL,
	[field_3] [int] NULL,
	[field_4] [datetime] NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_KillIPTemp] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[OrganSt]    Script Date: 2019/7/24 15:55:49 ******/
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
/****** Object:  Table [dbo].[Partner]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[Partner](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](50) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [int] NULL,
	[field_6] [varchar](100) NULL,
	[field_7] [varchar](200) NULL,
	[field_8] [varchar](2000) NULL,
	[field_9] [varchar](100) NULL,
	[field_10] [varchar](200) NULL,
	[field_11] [int] NULL,
	[field_12] [datetime] NULL,
	[field_13] [varchar](50) NULL,
	[field_14] [varchar](50) NULL,
	[field_15] [varchar](50) NULL,
	[field_16] [varchar](100) NULL,
	[field_17] [varchar](50) NULL,
	[field_18] [float] NULL,
	[field_19] [varchar](500) NULL,
	[field_20] [varchar](200) NULL,
	[field_21] [varchar](20) NULL,
	[field_22] [int] NULL,
	[field_23] [int] NULL,
	[field_24] [varchar](50) NULL,
	[field_25] [varchar](50) NULL,
	[field_26] [varchar](500) NULL,
	[field_27] [varchar](100) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_Partner] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[Qrcode]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[Qrcode](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](10) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](100) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](100) NULL,
	[field_6] [int] NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_Qrcode] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[SafeIP]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[SafeIP](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](20) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](1000) NULL,
	[field_6] [int] NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_SafeIP] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[SysCfg]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[SysCfg](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](50) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](50) NULL,
	[field_6] [varchar](2500) NULL,
	[field_7] [text] NULL,
	[field_8] [varchar](1500) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_SysCfg] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[SystemRole]    Script Date: 2019/7/24 15:55:49 ******/
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
/****** Object:  Table [dbo].[UABind]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[UABind](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](100) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](50) NULL,
	[field_6] [varchar](50) NULL,
	[field_7] [varchar](100) NULL,
	[field_8] [varchar](200) NULL,
	[field_9] [int] NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_UABind] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[VeriSMS]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[VeriSMS](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[field_1] [varchar](50) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](50) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [varchar](200) NULL,
	[field_6] [int] NULL,
	[field_7] [varchar](50) NULL,
	[CreateDate] [datetime] NULL,
 CONSTRAINT [PK_VeriSMS] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
/****** Object:  Table [dbo].[查询]    Script Date: 2019/7/24 15:55:49 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[查询](
	[id] [int] NOT NULL,
	[field_1] [varchar](100) NULL,
	[field_2] [int] NULL,
	[field_3] [varchar](50) NULL,
	[field_4] [varchar](50) NULL,
	[field_5] [int] NULL,
	[field_6] [int] NULL,
	[field_7] [varchar](500) NULL,
	[field_8] [varchar](100) NULL,
	[field_11] [varchar](1000) NULL,
	[field_12] [varchar](100) NULL,
	[field_13] [varchar](1000) NULL,
	[CreateDate] [datetime2](3) NULL
) ON [PRIMARY]

GO
SET ANSI_PADDING OFF
GO
ALTER TABLE [dbo].[FileLib] ADD  CONSTRAINT [DF_FileLib_Field_10]  DEFAULT ((0)) FOR [field_10]
GO
ALTER TABLE [dbo].[Partner] ADD  CONSTRAINT [DF_Partner_field_11]  DEFAULT ((0)) FOR [field_11]
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'菜单标题' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'AdminMenu', @level2type=N'COLUMN',@level2name=N'field_1'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'位置排序' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'AdminMenu', @level2type=N'COLUMN',@level2name=N'field_2'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'父级菜单' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'AdminMenu', @level2type=N'COLUMN',@level2name=N'field_5'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'是否显示' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'AdminMenu', @level2type=N'COLUMN',@level2name=N'field_6'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'菜单地址' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'AdminMenu', @level2type=N'COLUMN',@level2name=N'field_7'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'菜单 DNA' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'AdminMenu', @level2type=N'COLUMN',@level2name=N'field_8'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'订用角色' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'AdminMenu', @level2type=N'COLUMN',@level2name=N'field_9'
GO
EXEC sys.sp_addextendedproperty @name=N'MS_Description', @value=N'订用人员' , @level0type=N'SCHEMA',@level0name=N'dbo', @level1type=N'TABLE',@level1name=N'AdminMenu', @level2type=N'COLUMN',@level2name=N'field_10'
GO

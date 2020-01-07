alter table tk_order add totals decimal(10,2) not null default 0;

alter table tk_order_banquet_suborder add check_banquet_suborder_status int not null default 0;
alter table tk_order_banquet_suborder add check_banquet_suborder_remark varchar(255) not null default '';

alter table tk_order_wedding_suborder add wedding_total decimal(10,2) not null default 0;
alter table tk_order_wedding_suborder add wedding_category_amount int(11) not null default 0;
alter table tk_order_wedding_suborder add check_wedding_suborder_status int not null default 0;
alter table tk_order_wedding_suborder add check_wedding_suborder_remark varchar(255) not null default '';

alter table tk_order_banquet_receivables add check_banquet_receivable_status int not null default 0;
alter table tk_order_banquet_receivables add check_banquet_receivable_remark varchar(255) not null default '';

alter table tk_order_wedding_payment add wedding_supplier varchar(100) not null default '';

alter table tk_order_banquet add banquet_remark text;
alter table tk_order_wedding add wedding_remark text;

alter table tk_order_wedding_suborder add sub_wedding_remark text;

alter table tk_order_banquet_payment add banquet_payment_remark text;

alter table tk_order_wedding_payment add wedding_payment_remark text;
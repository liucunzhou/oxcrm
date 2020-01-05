alter table tk_order_banquet_suborder add check_banquet_suborder_status int not null default 0;
alter table tk_order_banquet_suborder add check_banquet_suborder_remark varchar(255) not null default '';

alter table tk_order_wedding_suborder add check_wedding_suborder_status int not null default 0;
alter table tk_order_wedding_suborder add check_wedding_suborder_remark varchar(255) not null default '';

alter table tk_order_banquet_receivables add check_banquet_receivable_status int not null default 0;
alter table tk_order_banquet_receivables add check_banquet_receivable_remark varchar(255) not null default '';

alter table tk_order_wedding_payment add wedding_supplier varchar(100) not null default '';
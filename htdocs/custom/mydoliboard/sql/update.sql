--- 3.0 feature
ALTER TABLE  llx_mydoliboardsheet	ADD	exportsheet 		varchar(64)  NULL DEFAULT NULL;
ALTER TABLE  llx_mydoliboardsheet	ADD	startexportcol 		integer NULL DEFAULT 0;
ALTER TABLE  llx_mydoliboardsheet	ADD	startexportrow 		integer NULL DEFAULT 0;

ALTER TABLE  llx_mydoliboard 		ADD	xlstemplate 		varchar(50) NULL DEFAULT NULL;
--- 2.0 feature
ALTER TABLE  llx_mydoliboardsheet	ADD	graphtype 			text NULL DEFAULT NULL;
ALTER TABLE  llx_mydoliboard 		ADD	elementtab 			varchar(50) NULL DEFAULT NULL;
-- Disable journal type 2:sale / 3:purchase / 4:bank / 5:expense report
UPDATE llx_accounting_journal set active = 0 WHERE nature = 2;
UPDATE llx_accounting_journal set active = 0 WHERE nature = 3;
UPDATE llx_accounting_journal set active = 0 WHERE nature = 4;
UPDATE llx_accounting_journal set active = 0 WHERE nature = 5;
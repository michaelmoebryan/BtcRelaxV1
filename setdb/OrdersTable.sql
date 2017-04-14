CREATE VIEW `vwOrders` AS
SELECT 
    IdOrder,
    CreateDate,
    EndDate,
    OrderState,
    idSaller,
    BTCPrice,
    PricingDate,
    InvoiceAddress,
    idCreator
FROM
    Orders;
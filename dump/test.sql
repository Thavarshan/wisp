/* Two financial stock indexes, FSIA and FSIB each contain a different unique set of companies. Each index is tored in a separate table with different schema definition.
- The FSIA table provides only the market capitalisation of the companies
- the FSIB table provides the share price and shares outstanding. Market capitalisation can be calculated as follows: Share Price * Shares Outstanding. */

The two tables are as follows:

FSIA table:
```
TABLE fsia
companyName VARCHAR(30) NOT NULL PRIMARY KEY
marketCapitalisation FLOAT NOT NULL

```

FSIB table:
```
TABLE fsib
companyName VARCHAR(30) NOT NULL PRIMARY KEY
sharePrice FLOAT NOT NULL
shareOutstanding INTEGER NOT NULL
```

Write a query that returns the name and market capitalisation of each company, orderd by market capitalisation, largest to smallest

SELECT companyName, marketCapitalisation
FROM fsia
UNION
SELECT companyName, sharePrice * shareOutstanding AS marketCapitalisation
FROM fsib
ORDER BY marketCapitalisation DESC;

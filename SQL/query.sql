--1 Determinare le vendite nel reparto macelleria effettuate in un supermercato nella giornata del 12 maggio del 2020
SELECT p.nome, qta,"prezzoUnità"
FROM scontrini join vendite v on scontrini.id = v.scontrino join prodotti p on v.prodotto = p.id
WHERE prodotto IN (SELECT prodotto FROM "contenutoReparto" WHERE "nomeReparto"='Macelleria')
    AND "dataEmissione"='2020-05-12';

--2 Determinare per i prodotti del reparto cancelleria che richiedono di essere riordinati il tempo minimo di riapprovvigionamento
--Totale di un prodotto
CREATE VIEW totaleProdotto AS
    SELECT sum(qta) as qtaTotale, prodotto, "supermercatoReparto"
    FROM "contenutoReparto"
    GROUP BY prodotto, "supermercatoReparto";

--Prodotti da Riordinare
CREATE VIEW dariordinare AS
    SELECT id
    FROM totaleProdotto JOIN prodotti ON id=prodotto
    WHERE qtaTotale < soglia;

--Tempo minimo di riaprovvigionamento
SELECT MIN("giorniConsegnaStimata"), ordini.prodotto
FROM ordini JOIN "contenutoReparto" ON ordini.prodotto="contenutoReparto".prodotto
WHERE ordini.prodotto IN (SELECT id FROM dariordinare) AND "nomeReparto"='Cancelleria'
GROUP BY ordini.prodotto;

--3 Determinare i prodotti preparati nel supermercato assemblati usando almeno un altro prodotto preparato nel supermercato
SELECT DISTINCT  p.nome, p.prezzo
FROM prodotti p JOIN ingredienti i on p.id = i."prodottoFinale" join prodotti p2 ON p2.id=i.ingrediente
WHERE p2.assemblato=True;

--Aggiunte all'esame

--1 Restituire le informazioni dei clienti che hanno prenotato due o più premi speciali e sono in attesa della disponibilità relativa a questi per poterli ritirare.
SELECT nome, cognome, "nTessera", "saldoPunti"
FROM clienti join prenotazioni p on clienti."nTessera" = p.cliente join persone p2 on clienti.persona = p2."codF"
WHERE disponibile=false
GROUP BY nome, cognome, "nTessera", "saldoPunti"
HAVING count(cliente)>=2;

--2 Restituire i clienti che hanno acquisito premi speciali usando sempre esclusivamente la modalità punti.
SELECT DISTINCT "nTessera"
FROM clienti join prenotazioni p on clienti."nTessera" = p.cliente
WHERE modalita='punti'
EXCEPT
SELECT DISTINCT "nTessera"
FROM clienti join prenotazioni p on clienti."nTessera" = p.cliente
WHERE modalita='mista'


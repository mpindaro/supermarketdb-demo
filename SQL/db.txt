--
-- PostgreSQL database dump
--

-- Dumped from database version 12.1 (Ubuntu 12.1-1.pgdg19.04+1)
-- Dumped by pg_dump version 12.1 (Ubuntu 12.1-1.pgdg19.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: CatenaDiSupermercati; Type: DATABASE; Schema: -; Owner: postgres
--

CREATE DATABASE "CatenaDiSupermercati" WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'it_IT.UTF-8' LC_CTYPE = 'it_IT.UTF-8';


ALTER DATABASE "CatenaDiSupermercati" OWNER TO postgres;

\connect "CatenaDiSupermercati"

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: casse; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.casse (
    supermercato character varying NOT NULL,
    "nCassa" integer NOT NULL
);


ALTER TABLE public.casse OWNER TO postgres;

--
-- Name: cassieri; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cassieri (
    impiegato integer NOT NULL,
    "supermercatoCassa" character varying NOT NULL,
    "nCassa" integer NOT NULL,
    turno character varying NOT NULL
);


ALTER TABLE public.cassieri OWNER TO postgres;

--
-- Name: clienti; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.clienti (
    "nTessera" character varying NOT NULL,
    "saldoPunti" integer NOT NULL,
    persona character varying(16) NOT NULL,
    CONSTRAINT puntipositivi CHECK (("saldoPunti" >= 0))
);


ALTER TABLE public.clienti OWNER TO postgres;

--
-- Name: contenutoReparto; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public."contenutoReparto" (
    "idBatch" integer NOT NULL,
    "supermercatoReparto" character varying NOT NULL,
    "nomeReparto" character varying NOT NULL,
    prodotto integer NOT NULL,
    "dataScadenza" date,
    qta integer NOT NULL,
    CONSTRAINT qtacontenutoinunreparto CHECK ((qta >= 0))
);


ALTER TABLE public."contenutoReparto" OWNER TO postgres;

--
-- Name: prodotti; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.prodotti (
    id bigint NOT NULL,
    nome character varying NOT NULL,
    prezzo double precision NOT NULL,
    categoria character varying NOT NULL,
    assemblato boolean DEFAULT false NOT NULL,
    soglia integer NOT NULL,
    CONSTRAINT soglianonnegativa CHECK ((soglia >= 1))
);


ALTER TABLE public.prodotti OWNER TO postgres;

--
-- Name: totaleprodotto; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.totaleprodotto AS
 SELECT sum("contenutoReparto".qta) AS qtatotale,
    "contenutoReparto".prodotto,
    "contenutoReparto"."supermercatoReparto"
   FROM public."contenutoReparto"
  GROUP BY "contenutoReparto".prodotto, "contenutoReparto"."supermercatoReparto";


ALTER TABLE public.totaleprodotto OWNER TO postgres;

--
-- Name: dariordinare; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.dariordinare AS
 SELECT prodotti.id
   FROM (public.totaleprodotto
     JOIN public.prodotti ON ((prodotti.id = totaleprodotto.prodotto)))
  WHERE (totaleprodotto.qtatotale < prodotti.soglia);


ALTER TABLE public.dariordinare OWNER TO postgres;

--
-- Name: fornitori; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.fornitori (
    iva character varying(11) NOT NULL,
    "ragioneSociale" character varying NOT NULL,
    indirizzo character varying NOT NULL,
    telefono character varying(12) NOT NULL,
    mail character varying NOT NULL,
    pagamento integer NOT NULL
);


ALTER TABLE public.fornitori OWNER TO postgres;

--
-- Name: impiegati; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.impiegati (
    matricola integer NOT NULL,
    "dataAssunzione" date NOT NULL,
    mansione character varying NOT NULL,
    livello integer NOT NULL,
    supermercato character varying,
    "nomeReparto" character varying,
    persona character varying(16) NOT NULL,
    CONSTRAINT dataassunzionenonfutura CHECK (("dataAssunzione" <= CURRENT_DATE)),
    CONSTRAINT livelloimpiegato CHECK (((livello >= 1) AND (livello <= 7)))
);


ALTER TABLE public.impiegati OWNER TO postgres;

--
-- Name: impiegati_matricola_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.impiegati_matricola_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.impiegati_matricola_seq OWNER TO postgres;

--
-- Name: impiegati_matricola_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.impiegati_matricola_seq OWNED BY public.impiegati.matricola;


--
-- Name: ingredienti; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ingredienti (
    ingrediente integer NOT NULL,
    "prodottoFinale" integer NOT NULL,
    qta integer NOT NULL,
    CONSTRAINT stimatempogiornipositiva CHECK ((qta > 0))
);


ALTER TABLE public.ingredienti OWNER TO postgres;

--
-- Name: metodoPagamento; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public."metodoPagamento" (
    id integer NOT NULL,
    tipo character varying NOT NULL
);


ALTER TABLE public."metodoPagamento" OWNER TO postgres;

--
-- Name: metodoPagamento_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public."metodoPagamento_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."metodoPagamento_id_seq" OWNER TO postgres;

--
-- Name: metodoPagamento_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public."metodoPagamento_id_seq" OWNED BY public."metodoPagamento".id;


--
-- Name: orari; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.orari (
    giorni character varying NOT NULL,
    supermercato character varying NOT NULL,
    "oraApertura" time without time zone NOT NULL,
    "oraChiusura" time without time zone NOT NULL,
    CONSTRAINT primaaprepoichiude CHECK (("oraChiusura" > "oraApertura"))
);


ALTER TABLE public.orari OWNER TO postgres;

--
-- Name: orariStraordinari; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public."orariStraordinari" (
    supermercato character varying NOT NULL,
    data character varying NOT NULL,
    "oraApertura" time without time zone NOT NULL,
    "oraChiusura" time without time zone NOT NULL,
    CONSTRAINT primaaprepoichiudestraordinario CHECK (("oraChiusura" > "oraApertura"))
);


ALTER TABLE public."orariStraordinari" OWNER TO postgres;

--
-- Name: ordini; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ordini (
    "codiceInterno" integer NOT NULL,
    "dataOrdine" date NOT NULL,
    qta integer NOT NULL,
    fornitore character varying(16) NOT NULL,
    prodotto integer NOT NULL,
    supermercato character varying NOT NULL,
    "dataScadenza" date,
    "giorniConsegnaStimata" integer NOT NULL,
    CONSTRAINT dataordinenonfutura CHECK (("dataOrdine" <= CURRENT_DATE)),
    CONSTRAINT datascadenzafutura CHECK (("dataScadenza" >= CURRENT_DATE)),
    CONSTRAINT qtaordinestrettamentepositiva CHECK ((qta > 0)),
    CONSTRAINT stimatempogiornipositiva CHECK (("giorniConsegnaStimata" >= 0))
);


ALTER TABLE public.ordini OWNER TO postgres;

--
-- Name: ordiniPremi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public."ordiniPremi" (
    premio integer NOT NULL,
    cliente character varying NOT NULL,
    "dataOrdine" date NOT NULL,
    qta integer NOT NULL,
    CONSTRAINT qtaordinepremiopositiva CHECK ((qta >= 1))
);


ALTER TABLE public."ordiniPremi" OWNER TO postgres;

--
-- Name: persone; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.persone (
    "codF" character varying(16) NOT NULL,
    nome character varying(20) NOT NULL,
    cognome character varying(20) NOT NULL,
    indirizzo character varying NOT NULL,
    telefono character varying(12) NOT NULL,
    "dataNascita" date NOT NULL,
    mail character varying NOT NULL
);


ALTER TABLE public.persone OWNER TO postgres;

--
-- Name: premi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.premi (
    codice bigint NOT NULL,
    nome character varying NOT NULL,
    categoria character varying NOT NULL,
    "puntiNecessari" integer NOT NULL,
    "dataInizioVadilita" date NOT NULL,
    "dataFineValidita" date NOT NULL,
    CONSTRAINT puntinecessariperunpremiomaggioridi0 CHECK (("puntiNecessari" > 0)),
    CONSTRAINT "validitàpremioinunintervalloesistente" CHECK (("dataInizioVadilita" < "dataFineValidita"))
);


ALTER TABLE public.premi OWNER TO postgres;

--
-- Name: premiSpeciali; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public."premiSpeciali" (
    premio bigint NOT NULL,
    "partePunti" integer NOT NULL,
    "parteDenaro" double precision NOT NULL,
    CONSTRAINT denaropositivo CHECK (("parteDenaro" > (0)::double precision)),
    CONSTRAINT puntiscontatipositivi CHECK (("partePunti" > 0))
);


ALTER TABLE public."premiSpeciali" OWNER TO postgres;

--
-- Name: prenotazioni; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.prenotazioni (
    "premioSpeciale" bigint NOT NULL,
    supemercato character varying NOT NULL,
    cliente character varying NOT NULL,
    "timestampOrdine" timestamp without time zone NOT NULL,
    modalita character varying NOT NULL,
    qta integer NOT NULL,
    codice character varying NOT NULL,
    disponibile boolean DEFAULT false NOT NULL,
    CONSTRAINT "modalitàpuntiomista" CHECK ((((modalita)::text = 'punti'::text) OR ((modalita)::text = 'mista'::text))),
    CONSTRAINT qtapositiva CHECK ((qta > 0))
);


ALTER TABLE public.prenotazioni OWNER TO postgres;

--
-- Name: punti; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.punti (
    prodotto integer NOT NULL,
    "dataInserimento" date NOT NULL,
    "dataScadenza" date NOT NULL,
    "nPunti" integer NOT NULL,
    CONSTRAINT datainserimentoprodottopuntiprimadatascadenzapunti CHECK (("dataInserimento" <= "dataScadenza")),
    CONSTRAINT npuntichedaunprodottopositivi CHECK (("nPunti" >= 1))
);


ALTER TABLE public.punti OWNER TO postgres;

--
-- Name: reparti; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.reparti (
    supermercato character varying NOT NULL,
    nome character varying NOT NULL
);


ALTER TABLE public.reparti OWNER TO postgres;

--
-- Name: scontrini; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.scontrini (
    id integer NOT NULL,
    "dataEmissione" date NOT NULL,
    pagamento integer NOT NULL,
    "nCassa" integer NOT NULL,
    "supermercatoCassa" character varying NOT NULL,
    cliente character varying,
    prenotazione character varying,
    CONSTRAINT scontrininonnelfuturo CHECK (("dataEmissione" <= CURRENT_DATE))
);


ALTER TABLE public.scontrini OWNER TO postgres;

--
-- Name: scortePremi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public."scortePremi" (
    supermercato character varying NOT NULL,
    premio bigint NOT NULL,
    qta integer NOT NULL,
    CONSTRAINT qtapositivaougualeazero CHECK ((qta >= 0))
);


ALTER TABLE public."scortePremi" OWNER TO postgres;

--
-- Name: supermercati; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.supermercati (
    codice character varying NOT NULL,
    metriquadri double precision NOT NULL,
    indirizzo character varying
);


ALTER TABLE public.supermercati OWNER TO postgres;

--
-- Name: turni; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.turni (
    impiegato integer NOT NULL,
    giorno character varying NOT NULL,
    "inizioOra" time without time zone NOT NULL,
    "fineOra" time without time zone NOT NULL,
    CONSTRAINT eungiornodellasettimana CHECK ((((giorno)::text = 'Lunedi'::text) OR ((giorno)::text = 'Martedi'::text) OR ((giorno)::text = 'Mercoledi'::text) OR ((giorno)::text = 'Giovedi'::text) OR ((giorno)::text = 'Venerdi'::text) OR ((giorno)::text = 'Sabato'::text) OR ((giorno)::text = 'Domenica'::text)))
);


ALTER TABLE public.turni OWNER TO postgres;

--
-- Name: vendite; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.vendite (
    scontrino integer NOT NULL,
    prodotto integer NOT NULL,
    qta integer NOT NULL,
    "prezzoUnità" double precision NOT NULL,
    CONSTRAINT prezzovenditaminimo0 CHECK (("prezzoUnità" >= (0)::double precision)),
    CONSTRAINT vendutoalmenounprodtto CHECK ((qta > 0))
);


ALTER TABLE public.vendite OWNER TO postgres;

--
-- Name: impiegati matricola; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.impiegati ALTER COLUMN matricola SET DEFAULT nextval('public.impiegati_matricola_seq'::regclass);


--
-- Name: metodoPagamento id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."metodoPagamento" ALTER COLUMN id SET DEFAULT nextval('public."metodoPagamento_id_seq"'::regclass);


--
-- Data for Name: casse; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.casse VALUES ('01B109MI', 1);
INSERT INTO public.casse VALUES ('01B109MI', 2);
INSERT INTO public.casse VALUES ('02N120MI', 1);
INSERT INTO public.casse VALUES ('02N120MI', 2);


--
-- Data for Name: cassieri; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.cassieri VALUES (3, '01B109MI', 1, 'Mattina');
INSERT INTO public.cassieri VALUES (4, '01B109MI', 2, 'Pomeriggio');
INSERT INTO public.cassieri VALUES (5, '02N120MI', 1, 'Pomeriggio');
INSERT INTO public.cassieri VALUES (6, '02N120MI', 2, 'Mattina');


--
-- Data for Name: clienti; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.clienti VALUES ('1', 4001, 'CTTMLA74B60F205B');
INSERT INTO public.clienti VALUES ('2', 1474, 'BCCMRT79E20F205D');
INSERT INTO public.clienti VALUES ('3', 535, 'MNFVRN81P51F205H');
INSERT INTO public.clienti VALUES ('4', 6611, 'DNZCMR70B44F205D');
INSERT INTO public.clienti VALUES ('5', 268, 'CLMLTR67P23F205D');
INSERT INTO public.clienti VALUES ('6', 6315, 'CLBNLD61C68F205B');
INSERT INTO public.clienti VALUES ('7', 4469, 'CLMPLP88E53F205J');
INSERT INTO public.clienti VALUES ('8', 3261, 'TRVDGN62M51F205F');
INSERT INTO public.clienti VALUES ('9', 4247, 'DNZMRZ93H23F205F');
INSERT INTO public.clienti VALUES ('10', 2956, 'GRDMLA69H45F205G');
INSERT INTO public.clienti VALUES ('11', 2575, 'PDVRTL89T06F205I');
INSERT INTO public.clienti VALUES ('12', 838, 'PGNLRN67D57F205F');
INSERT INTO public.clienti VALUES ('13', 438, 'MZZFSC64P49F205Z');
INSERT INTO public.clienti VALUES ('14', 2396, 'RMNVTR70A23F205M');
INSERT INTO public.clienti VALUES ('15', 1521, 'BCCSLA64B47F205O');
INSERT INTO public.clienti VALUES ('16', 6365, 'PLRPLA95A08F205G');
INSERT INTO public.clienti VALUES ('17', 1945, 'BRSSLS63P20F205M');
INSERT INTO public.clienti VALUES ('18', 2098, 'NDRRHL96H10F205P');
INSERT INTO public.clienti VALUES ('19', 3742, 'NPLFST98T57F205H');
INSERT INTO public.clienti VALUES ('20', 5987, 'LCCCSP74P57F205F');
INSERT INTO public.clienti VALUES ('21', 6281, 'PCCCST90S58F205T');
INSERT INTO public.clienti VALUES ('22', 4045, 'GNVPCP76L11F205U');
INSERT INTO public.clienti VALUES ('23', 6184, 'CLMFSC76B25F205K');
INSERT INTO public.clienti VALUES ('24', 5429, 'TRNBNC00R23F205L');
INSERT INTO public.clienti VALUES ('25', 5255, 'LMBMLD72B51F205E');
INSERT INTO public.clienti VALUES ('26', 7827, 'DNSLCU98L21F205W');
INSERT INTO public.clienti VALUES ('27', 3636, 'PDVNLN94D04F205G');
INSERT INTO public.clienti VALUES ('28', 6944, 'FRRLLL58T51F205X');
INSERT INTO public.clienti VALUES ('29', 5835, 'BRSBLN65L44F205D');
INSERT INTO public.clienti VALUES ('30', 4337, 'DVDRNI57T41F205L');
INSERT INTO public.clienti VALUES ('31', 3295, 'BCCDRT73M11F205V');
INSERT INTO public.clienti VALUES ('32', 3633, 'GRCGTT66H54F205V');
INSERT INTO public.clienti VALUES ('33', 5652, 'GLLMRN93D46F205G');
INSERT INTO public.clienti VALUES ('34', 8390, 'CNTVRN80T31F205D');
INSERT INTO public.clienti VALUES ('35', 4419, 'LCCRME59R11F205A');
INSERT INTO public.clienti VALUES ('36', 8487, 'FRRMTN67T58F205E');
INSERT INTO public.clienti VALUES ('37', 7439, 'DLLSVT55L69F205L');
INSERT INTO public.clienti VALUES ('38', 3243, 'SGSLSE62T23F205Q');
INSERT INTO public.clienti VALUES ('39', 3532, 'DLLLVI96D46F205X');
INSERT INTO public.clienti VALUES ('40', 5030, 'BLLNNF88L04F205M');
INSERT INTO public.clienti VALUES ('41', 6017, 'GRCLVE91D07F205B');
INSERT INTO public.clienti VALUES ('42', 4635, 'LFNPNC99E12F205E');
INSERT INTO public.clienti VALUES ('43', 722, 'TRVRFN52D12F205F');
INSERT INTO public.clienti VALUES ('44', 5509, 'PGNSLL68S46F205J');
INSERT INTO public.clienti VALUES ('45', 7614, 'PGNMRN73E16F205A');
INSERT INTO public.clienti VALUES ('46', 5633, 'MLNCCT57E59F205X');
INSERT INTO public.clienti VALUES ('47', 7198, 'MRNRRT65T02F205F');
INSERT INTO public.clienti VALUES ('48', 6200, 'SBBFDR51A57F205Y');
INSERT INTO public.clienti VALUES ('49', 8893, 'NDRBTE69C13F205G');
INSERT INTO public.clienti VALUES ('50', 4793, 'NCCGRD57A08F205D');
INSERT INTO public.clienti VALUES ('51', 7955, 'RSSMRO55T59F205X');
INSERT INTO public.clienti VALUES ('52', 2072, 'BLLFBR68E11F205P');
INSERT INTO public.clienti VALUES ('53', 6415, 'CLBLVC97T24F205L');
INSERT INTO public.clienti VALUES ('54', 198, 'NGLDND73S59F205H');
INSERT INTO public.clienti VALUES ('55', 8086, 'CSTLNS83S13F205V');
INSERT INTO public.clienti VALUES ('56', 3744, 'PSNNLC00M70F205G');
INSERT INTO public.clienti VALUES ('57', 7842, 'PLRPTL00P61F205R');
INSERT INTO public.clienti VALUES ('58', 2129, 'CCCGGR50B09F205D');
INSERT INTO public.clienti VALUES ('59', 7710, 'MNLVND74P05F205J');
INSERT INTO public.clienti VALUES ('60', 3793, 'TRVRSL55M55F205C');
INSERT INTO public.clienti VALUES ('61', 6310, 'LGGGMN58L24F205R');
INSERT INTO public.clienti VALUES ('62', 4907, 'CTTLNZ48C62F205U');
INSERT INTO public.clienti VALUES ('63', 332, 'MRNMLL74C31F205E');


--
-- Data for Name: contenutoReparto; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public."contenutoReparto" VALUES (1028, '01B109MI', 'Igiene Persona', 5641023, NULL, 93);
INSERT INTO public."contenutoReparto" VALUES (1029, '01B109MI', 'Igiene Persona', 5402283, NULL, 29);
INSERT INTO public."contenutoReparto" VALUES (1030, '01B109MI', 'Igiene Persona', 5543413, NULL, 14);
INSERT INTO public."contenutoReparto" VALUES (1031, '01B109MI', 'Casa e detersivi', 5392341, NULL, 102);
INSERT INTO public."contenutoReparto" VALUES (1032, '01B109MI', 'Casa e detersivi', 5384648, NULL, 52);
INSERT INTO public."contenutoReparto" VALUES (1033, '01B109MI', 'Casa e detersivi', 5396751, NULL, 140);
INSERT INTO public."contenutoReparto" VALUES (1034, '01B109MI', 'Cancelleria', 5388822, NULL, 242);
INSERT INTO public."contenutoReparto" VALUES (1035, '01B109MI', 'Cancelleria', 5402665, NULL, 220);
INSERT INTO public."contenutoReparto" VALUES (1036, '01B109MI', 'Cancelleria', 5402618, NULL, 129);
INSERT INTO public."contenutoReparto" VALUES (1081, '02N120MI', 'Igiene Persona', 5641023, NULL, 230);
INSERT INTO public."contenutoReparto" VALUES (1082, '02N120MI', 'Igiene Persona', 5402283, NULL, 53);
INSERT INTO public."contenutoReparto" VALUES (1083, '02N120MI', 'Igiene Persona', 5543413, NULL, 216);
INSERT INTO public."contenutoReparto" VALUES (1084, '02N120MI', 'Casa e detersivi', 5392341, NULL, 81);
INSERT INTO public."contenutoReparto" VALUES (1085, '02N120MI', 'Casa e detersivi', 5384648, NULL, 245);
INSERT INTO public."contenutoReparto" VALUES (1086, '02N120MI', 'Casa e detersivi', 5396751, NULL, 26);
INSERT INTO public."contenutoReparto" VALUES (1087, '02N120MI', 'Cancelleria', 5388822, NULL, 168);
INSERT INTO public."contenutoReparto" VALUES (1088, '02N120MI', 'Cancelleria', 5402665, NULL, 96);
INSERT INTO public."contenutoReparto" VALUES (1089, '02N120MI', 'Cancelleria', 5402618, NULL, 282);
INSERT INTO public."contenutoReparto" VALUES (1001, '01B109MI', 'Frutta e Verdura', 5382198, '2020-07-30', 52);
INSERT INTO public."contenutoReparto" VALUES (1002, '01B109MI', 'Frutta e Verdura', 5385170, '2020-07-30', 212);
INSERT INTO public."contenutoReparto" VALUES (1011, '01B109MI', 'Macelleria', 5428255, '2020-08-20', 198);
INSERT INTO public."contenutoReparto" VALUES (1102, '02N120MI', 'Latticini', 5377809, '2020-07-30', 36);
INSERT INTO public."contenutoReparto" VALUES (1077, '02N120MI', 'Surgelati e gelati', 5398136, '2021-07-01', 93);
INSERT INTO public."contenutoReparto" VALUES (1025, '01B109MI', 'Snack e colazione', 5389264, '2020-11-17', 59);
INSERT INTO public."contenutoReparto" VALUES (1037, '01B109MI', 'Pasta e riso', 5400006, '2021-12-22', 122);
INSERT INTO public."contenutoReparto" VALUES (1022, '01B109MI', 'Surgelati e gelati', 5731069, '2021-07-21', 108);
INSERT INTO public."contenutoReparto" VALUES (1096, '02N120MI', 'Condimenti', 5383776, '2022-07-15', 206);
INSERT INTO public."contenutoReparto" VALUES (1021, '01B109MI', 'Gastronomia', 5515756, '2020-07-31', 183);
INSERT INTO public."contenutoReparto" VALUES (1016, '01B109MI', 'Pane e Pasticceria', 5561086, '2020-09-10', 210);
INSERT INTO public."contenutoReparto" VALUES (1075, '02N120MI', 'Surgelati e gelati', 5731069, '2021-07-21', 179);
INSERT INTO public."contenutoReparto" VALUES (1069, '02N120MI', 'Pane e Pasticceria', 5561086, '2021-07-21', 276);
INSERT INTO public."contenutoReparto" VALUES (1072, '02N120MI', 'Gastronomia', 5722897, '2020-07-31', 236);
INSERT INTO public."contenutoReparto" VALUES (1073, '02N120MI', 'Gastronomia', 5527406, '2020-07-31', 297);
INSERT INTO public."contenutoReparto" VALUES (1026, '01B109MI', 'Snack e colazione', 5714217, '2020-11-20', 291);
INSERT INTO public."contenutoReparto" VALUES (1023, '01B109MI', 'Surgelati e gelati', 5386365, '2021-07-05', 68);
INSERT INTO public."contenutoReparto" VALUES (1103, '02N120MI', 'Latticini', 5574707, '2020-07-29', 241);
INSERT INTO public."contenutoReparto" VALUES (1013, '01B109MI', 'Latticini', 5419992, '2020-08-19', 120);
INSERT INTO public."contenutoReparto" VALUES (1068, '02N120MI', 'Latticini', 5520562, '2020-08-24', 81);
INSERT INTO public."contenutoReparto" VALUES (1070, '02N120MI', 'Pane e Pasticceria', 5383546, '2021-07-05', 102);
INSERT INTO public."contenutoReparto" VALUES (1027, '01B109MI', 'Snack e colazione', 5513931, '2020-10-31', 7);
INSERT INTO public."contenutoReparto" VALUES (1104, '02N120MI', 'Latticini', 5574707, '2020-07-28', 144);
INSERT INTO public."contenutoReparto" VALUES (1017, '01B109MI', 'Pane e Pasticceria', 5383546, '2020-09-22', 219);
INSERT INTO public."contenutoReparto" VALUES (1059, '02N120MI', 'Pesce e Sushi', 5386477, '2020-07-31', 209);
INSERT INTO public."contenutoReparto" VALUES (1052, '01B109MI', 'Snack e colazione', 5429355, '2020-09-16', 26);
INSERT INTO public."contenutoReparto" VALUES (1078, '02N120MI', 'Snack e colazione', 5389264, '2020-12-15', 219);
INSERT INTO public."contenutoReparto" VALUES (1046, '01B109MI', 'Snack e colazione', 5409915, '2024-07-19', 9);
INSERT INTO public."contenutoReparto" VALUES (1039, '01B109MI', 'Pesce e Sushi', 5383211, '2020-07-28', 23);
INSERT INTO public."contenutoReparto" VALUES (1066, '02N120MI', 'Latticini', 5419992, '2020-08-03', 97);
INSERT INTO public."contenutoReparto" VALUES (1105, '02N120MI', 'Snack e colazione', 5429355, '2021-07-29', 33);
INSERT INTO public."contenutoReparto" VALUES (1048, '01B109MI', 'Condimenti', 5424211, '2025-07-23', 239);
INSERT INTO public."contenutoReparto" VALUES (1095, '02N120MI', 'Condimenti', 5398830, '2022-07-15', 6);
INSERT INTO public."contenutoReparto" VALUES (1074, '02N120MI', 'Gastronomia', 5515756, '2020-07-31', 146);
INSERT INTO public."contenutoReparto" VALUES (1079, '02N120MI', 'Snack e colazione', 5714217, '2020-10-12', 168);
INSERT INTO public."contenutoReparto" VALUES (1097, '02N120MI', 'Snack e colazione', 5381668, '2020-10-14', 155);
INSERT INTO public."contenutoReparto" VALUES (1006, '01B109MI', 'Pesce e Sushi', 5386477, '2020-07-26', 33);
INSERT INTO public."contenutoReparto" VALUES (1007, '01B109MI', 'Pesce e Sushi', 5735522, '2020-07-26', 160);
INSERT INTO public."contenutoReparto" VALUES (1018, '01B109MI', 'Pane e Pasticceria', 5410134, '2020-09-19', 250);
INSERT INTO public."contenutoReparto" VALUES (1043, '01B109MI', 'Condimenti', 5383776, '2025-01-20', 232);
INSERT INTO public."contenutoReparto" VALUES (1064, '02N120MI', 'Macelleria', 5428255, '2020-08-05', 229);
INSERT INTO public."contenutoReparto" VALUES (1071, '02N120MI', 'Pane e Pasticceria', 5410134, '2021-07-22', 98);
INSERT INTO public."contenutoReparto" VALUES (1009, '01B109MI', 'Macelleria', 5745336, '2020-08-12', 75);
INSERT INTO public."contenutoReparto" VALUES (1042, '01B109MI', 'Condimenti', 5398830, '2024-06-12', 39);
INSERT INTO public."contenutoReparto" VALUES (1049, '01B109MI', 'Latticini', 5377809, '2020-07-31', 200);
INSERT INTO public."contenutoReparto" VALUES (1051, '01B109MI', 'Latticini', 5574707, '2020-07-30', 219);
INSERT INTO public."contenutoReparto" VALUES (1076, '02N120MI', 'Surgelati e gelati', 5386365, '2021-07-05', 258);
INSERT INTO public."contenutoReparto" VALUES (1010, '01B109MI', 'Macelleria', 5534682, '2020-08-04', 39);
INSERT INTO public."contenutoReparto" VALUES (1098, '02N120MI', 'Condimenti', 5391406, '2021-07-19', 194);
INSERT INTO public."contenutoReparto" VALUES (1044, '01B109MI', 'Snack e colazione', 5381668, '2021-07-12', 5);
INSERT INTO public."contenutoReparto" VALUES (1092, '02N120MI', 'Pesce e Sushi', 5383211, '2020-07-28', 49);
INSERT INTO public."contenutoReparto" VALUES (1090, '02N120MI', 'Pasta e riso', 5400006, '2024-07-23', 211);
INSERT INTO public."contenutoReparto" VALUES (1045, '01B109MI', 'Condimenti', 5391406, '2025-07-18', 6);
INSERT INTO public."contenutoReparto" VALUES (1065, '02N120MI', 'Macelleria', 5388593, '2020-08-05', 83);
INSERT INTO public."contenutoReparto" VALUES (1019, '01B109MI', 'Gastronomia', 5722897, '2020-07-29', 179);
INSERT INTO public."contenutoReparto" VALUES (1099, '02N120MI', 'Snack e colazione', 5409915, '2020-09-16', 270);
INSERT INTO public."contenutoReparto" VALUES (1062, '02N120MI', 'Macelleria', 5745336, '2020-08-05', 253);
INSERT INTO public."contenutoReparto" VALUES (1063, '02N120MI', 'Macelleria', 5534682, '2020-08-05', 193);
INSERT INTO public."contenutoReparto" VALUES (1020, '01B109MI', 'Gastronomia', 5527406, '2020-07-30', 9);
INSERT INTO public."contenutoReparto" VALUES (1008, '01B109MI', 'Pesce e Sushi', 5772507, '2020-07-27', 225);
INSERT INTO public."contenutoReparto" VALUES (1061, '02N120MI', 'Pesce e Sushi', 5772507, '2020-07-29', 182);
INSERT INTO public."contenutoReparto" VALUES (1012, '01B109MI', 'Macelleria', 5388593, '2020-07-30', 282);
INSERT INTO public."contenutoReparto" VALUES (1014, '01B109MI', 'Latticini', 5377561, '2020-08-19', 22);
INSERT INTO public."contenutoReparto" VALUES (1015, '01B109MI', 'Latticini', 5520562, '2020-08-19', 113);
INSERT INTO public."contenutoReparto" VALUES (1101, '02N120MI', 'Condimenti', 5424211, '2020-10-12', 188);
INSERT INTO public."contenutoReparto" VALUES (1067, '02N120MI', 'Latticini', 5377561, '2020-08-10', 167);
INSERT INTO public."contenutoReparto" VALUES (1050, '01B109MI', 'Latticini', 5574707, '2020-07-31', 47);
INSERT INTO public."contenutoReparto" VALUES (1080, '02N120MI', 'Snack e colazione', 5513931, '2020-12-19', 258);
INSERT INTO public."contenutoReparto" VALUES (1060, '02N120MI', 'Pesce e Sushi', 5735522, '2020-07-30', 99);
INSERT INTO public."contenutoReparto" VALUES (1003, '01B109MI', 'Frutta e Verdura', 5381634, '2020-07-30', 119);
INSERT INTO public."contenutoReparto" VALUES (1038, '01B109MI', 'Frutta e Verdura', 5385204, '2020-07-31', 170);
INSERT INTO public."contenutoReparto" VALUES (1053, '01B109MI', 'Frutta e Verdura', 5400042, '2020-07-16', 151);
INSERT INTO public."contenutoReparto" VALUES (1041, '01B109MI', 'Frutta e Verdura', 5382651, '2020-08-18', 102);
INSERT INTO public."contenutoReparto" VALUES (1005, '01B109MI', 'Frutta e Verdura', 5837556, '2020-07-28', 4);
INSERT INTO public."contenutoReparto" VALUES (1040, '01B109MI', 'Frutta e Verdura', 5421684, '2020-08-19', 51);
INSERT INTO public."contenutoReparto" VALUES (1004, '01B109MI', 'Frutta e Verdura', 5391756, '2020-07-30', 89);
INSERT INTO public."contenutoReparto" VALUES (1054, '02N120MI', 'Frutta e Verdura', 5382198, '2020-07-30', 103);
INSERT INTO public."contenutoReparto" VALUES (1093, '02N120MI', 'Frutta e Verdura', 5421684, '2020-08-11', 251);
INSERT INTO public."contenutoReparto" VALUES (1091, '02N120MI', 'Frutta e Verdura', 5385204, '2020-07-31', 9);
INSERT INTO public."contenutoReparto" VALUES (1057, '02N120MI', 'Frutta e Verdura', 5391756, '2020-07-14', 36);
INSERT INTO public."contenutoReparto" VALUES (1056, '02N120MI', 'Frutta e Verdura', 5381634, '2020-07-07', 234);
INSERT INTO public."contenutoReparto" VALUES (1094, '02N120MI', 'Frutta e Verdura', 5382651, '2020-08-10', 4);
INSERT INTO public."contenutoReparto" VALUES (1106, '02N120MI', 'Frutta e Verdura', 5400042, '2020-07-30', 238);
INSERT INTO public."contenutoReparto" VALUES (1058, '02N120MI', 'Frutta e Verdura', 5837556, '2020-07-31', 110);
INSERT INTO public."contenutoReparto" VALUES (1055, '02N120MI', 'Frutta e Verdura', 5385170, '2020-07-07', 113);
INSERT INTO public."contenutoReparto" VALUES (1024, '01B109MI', 'Surgelati e gelati', 5398136, '2021-07-01', 197);


--
-- Data for Name: fornitori; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.fornitori VALUES ('IT005025912', 'Wickes Furniture', 'Piazza Bovio 150, Fontanaluccia', '0387 2286827', 'FantinoMilanesi@jourrapide.com', 4);
INSERT INTO public.fornitori VALUES ('IT006973001', 'Advansed Teksyztems', 'Via del Viminale 19, Sant''Antonio Di Santadi', '0329 6840291', 'GualtieroSiciliani@armyspy.com', 4);
INSERT INTO public.fornitori VALUES ('IT007541501', 'Cut Rite Lawn Care', 'Via Solfatara 57, Capriglia Irpina', '0371 1125440', 'SalvatoreCosta@jourrapide.com', 6);
INSERT INTO public.fornitori VALUES ('IT008203409', 'FlowerTime', 'Via Spalato 51, Ponte Tresa', '0379 0336127', 'ManuelaManfrin@cuvox.de', 7);
INSERT INTO public.fornitori VALUES ('IT009021700', 'Sunflower Market', 'Via Pasquale Scura 2, Acquedolci', '0340 7022199', 'AmbrogioCapon@superrito.com', 2);
INSERT INTO public.fornitori VALUES ('IT010216306', 'Elm Farm', 'Via Miguel de Cervantes 32, Abriola', '0356 4215729', 'IgorLongo@superrito.com', 6);
INSERT INTO public.fornitori VALUES ('IT010441203', 'Joseph Magnin', 'Via Stadera 4, Isola Di Fano', '0396 9655247', 'FeliceDellucci@fleckens.hu', 6);
INSERT INTO public.fornitori VALUES ('IT011146010', 'Network Air', 'Via Archimede 4, Ponte Di Verzuno', '0322 0165612', 'EnricoLori@teleworm.us', 7);
INSERT INTO public.fornitori VALUES ('IT012195608', 'Steak and Ale', 'Via Croce Rossa 65, Suni', '0330 1041468', 'LeonardoFolliero@einrot.com', 4);
INSERT INTO public.fornitori VALUES ('IT013902304', 'Olympic Sports', 'Via del Mascherone 102, Cagliari', '0373 9122019', 'FaustoSchiavone@superrito.com', 3);
INSERT INTO public.fornitori VALUES ('IT016549604', 'Garden Guru', 'Corso Garibaldi 52, Selva Di Sora', '0358 4401177', 'FortunataLucciano@jourrapide.com', 2);
INSERT INTO public.fornitori VALUES ('IT021183110', 'Rich and Happy', 'Via Stadera 68, Serravalle Di Carda', '0368 5413587', 'TizianoLettiere@gustr.com', 4);
INSERT INTO public.fornitori VALUES ('IT021211510', 'Home Centers', 'Via Licola Patria 95, Castel Baronia', '0320 2826064', 'EdvigeMilani@einrot.com', 4);
INSERT INTO public.fornitori VALUES ('IT024581602', 'William Wanamaker & Sons', 'Via Spalato 142, Olgiate Olona', '0314 4760323', 'FlaviaTrentino@cuvox.de', 3);
INSERT INTO public.fornitori VALUES ('IT072342509', 'Warner Brothers Studio Store', 'Via Venezia 14, San Demetrio Corone', '0376 6249564', 'RanieroMilani@superrito.com', 7);


--
-- Data for Name: impiegati; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.impiegati VALUES (1, '2014-07-09', 'Capo Magazziniere', 3, '01B109MI', 'Magazzino', 'DNZMRZ93H23F205F');
INSERT INTO public.impiegati VALUES (2, '2009-07-23', 'Capo Magazziniere', 3, '02N120MI', 'Magazzino', 'RMNVTR70A23F205M');
INSERT INTO public.impiegati VALUES (3, '2020-07-14', 'Cassiere', 4, NULL, NULL, 'LCCCSP74P57F205F');
INSERT INTO public.impiegati VALUES (4, '2017-07-22', 'Cassiere', 4, NULL, NULL, 'MZZFSC64P49F205Z');
INSERT INTO public.impiegati VALUES (5, '2019-10-17', 'Cassiere', 4, NULL, NULL, 'TRNBNC00R23F205L');
INSERT INTO public.impiegati VALUES (6, '2020-07-06', 'Cassiere', 4, NULL, NULL, 'GRDMLA69H45F205G');
INSERT INTO public.impiegati VALUES (8, '2020-06-15', 'Macellaio', 4, '01B109MI', 'Pesce e Sushi', 'CTTMLA74B60F205B');
INSERT INTO public.impiegati VALUES (9, '2015-07-28', 'Macellaio', 4, '01B109MI', 'Macelleria', 'BCCMRT79E20F205D');
INSERT INTO public.impiegati VALUES (10, '2017-10-05', 'Scaffalista', 5, '01B109MI', 'Latticini', 'MNFVRN81P51F205H');
INSERT INTO public.impiegati VALUES (11, '2010-11-21', 'Panettiere', 4, '01B109MI', 'Pane e Pasticceria', 'DNZCMR70B44F205D');
INSERT INTO public.impiegati VALUES (12, '2020-07-06', 'Scaffalista', 5, '01B109MI', 'Gastronomia', 'CLMLTR67P23F205D');
INSERT INTO public.impiegati VALUES (13, '2019-06-16', 'Scaffalista', 5, '01B109MI', 'Surgelati e gelati', 'CLBNLD61C68F205B');
INSERT INTO public.impiegati VALUES (17, '2016-02-14', 'Scaffalista', 5, '01B109MI', 'Snack e colazione', 'CLMPLP88E53F205J');
INSERT INTO public.impiegati VALUES (18, '2016-02-14', 'Scaffalista', 5, '01B109MI', 'Igiene Persona', 'TRVDGN62M51F205F');
INSERT INTO public.impiegati VALUES (21, '2016-02-14', 'Scaffalista', 5, '01B109MI', 'Cancelleria', 'PDVRTL89T06F205I');
INSERT INTO public.impiegati VALUES (22, '2016-02-14', 'Scaffalista', 5, '01B109MI', 'Condimenti', 'PGNLRN67D57F205F');
INSERT INTO public.impiegati VALUES (24, '2016-02-14', 'Scaffalista', 5, '02N120MI', 'Pesce e Sushi', 'PLRPLA95A08F205G');
INSERT INTO public.impiegati VALUES (25, '2016-02-14', 'Scaffalista', 5, '02N120MI', 'Macelleria', 'BRSSLS63P20F205M');
INSERT INTO public.impiegati VALUES (26, '2016-02-14', 'Scaffalista', 5, '02N120MI', 'Latticini', 'NDRRHL96H10F205P');
INSERT INTO public.impiegati VALUES (27, '2016-02-14', 'Scaffalista', 5, '02N120MI', 'Pane e Pasticceria', 'NPLFST98T57F205H');
INSERT INTO public.impiegati VALUES (28, '2016-02-14', 'Scaffalista', 5, '02N120MI', 'Gastronomia', 'PCCCST90S58F205T');
INSERT INTO public.impiegati VALUES (29, '2016-02-14', 'Scaffalista', 5, '02N120MI', 'Surgelati e gelati', 'GNVPCP76L11F205U');
INSERT INTO public.impiegati VALUES (30, '2016-02-14', 'Scaffalista', 5, '02N120MI', 'Snack e colazione', 'LMBMLD72B51F205E');
INSERT INTO public.impiegati VALUES (31, '2016-02-14', 'Scaffalista', 5, '02N120MI', 'Igiene Persona', 'DNSLCU98L21F205W');
INSERT INTO public.impiegati VALUES (32, '2016-02-14', 'Scaffalista', 5, '02N120MI', 'Casa e detersivi', 'FRRLLL58T51F205X');
INSERT INTO public.impiegati VALUES (33, '2016-02-14', 'Scaffalista', 5, '02N120MI', 'Cancelleria', 'BRSBLN65L44F205D');
INSERT INTO public.impiegati VALUES (34, '2016-02-14', 'Scaffalista', 5, '02N120MI', 'Condimenti', 'LMBMNT58E47F205L');
INSERT INTO public.impiegati VALUES (36, '2010-12-23', 'Responsabile', 2, '01B109MI', 'Pesce e Sushi', 'BCCDRT73M11F205V');
INSERT INTO public.impiegati VALUES (37, '2010-12-23', 'Responsabile', 2, '01B109MI', 'Macelleria', 'GRCGTT66H54F205V');
INSERT INTO public.impiegati VALUES (38, '2010-12-23', 'Responsabile', 2, '01B109MI', 'Latticini', 'GLLMRN93D46F205G');
INSERT INTO public.impiegati VALUES (39, '2010-12-23', 'Responsabile', 2, '01B109MI', 'Pane e Pasticceria', 'CNTVRN80T31F205D');
INSERT INTO public.impiegati VALUES (40, '2010-12-23', 'Responsabile', 2, '01B109MI', 'Gastronomia', 'LCCRME59R11F205A');
INSERT INTO public.impiegati VALUES (41, '2010-12-23', 'Responsabile', 2, '01B109MI', 'Surgelati e gelati', 'FRRMTN67T58F205E');
INSERT INTO public.impiegati VALUES (42, '2010-12-23', 'Responsabile', 2, '01B109MI', 'Snack e colazione', 'DLLSVT55L69F205L');
INSERT INTO public.impiegati VALUES (43, '2010-12-23', 'Responsabile', 2, '01B109MI', 'Igiene Persona', 'SGSLSE62T23F205Q');
INSERT INTO public.impiegati VALUES (44, '2010-12-23', 'Responsabile', 2, '01B109MI', 'Casa e detersivi', 'DLLLVI96D46F205X');
INSERT INTO public.impiegati VALUES (45, '2010-12-23', 'Responsabile', 2, '01B109MI', 'Cancelleria', 'BLLNNF88L04F205M');
INSERT INTO public.impiegati VALUES (46, '2010-12-23', 'Responsabile', 2, '01B109MI', 'Condimenti', 'GRCLVE91D07F205B');
INSERT INTO public.impiegati VALUES (48, '2010-12-23', 'Responsabile', 2, '02N120MI', 'Pesce e Sushi', 'TRVRFN52D12F205F');
INSERT INTO public.impiegati VALUES (49, '2010-12-23', 'Responsabile', 2, '02N120MI', 'Macelleria', 'PGNSLL68S46F205J');
INSERT INTO public.impiegati VALUES (50, '2010-12-23', 'Responsabile', 2, '02N120MI', 'Latticini', 'PGNMRN73E16F205A');
INSERT INTO public.impiegati VALUES (52, '2010-12-23', 'Responsabile', 2, '02N120MI', 'Gastronomia', 'MRNRRT65T02F205F');
INSERT INTO public.impiegati VALUES (53, '2010-12-23', 'Responsabile', 2, '02N120MI', 'Surgelati e gelati', 'SBBFDR51A57F205Y');
INSERT INTO public.impiegati VALUES (54, '2010-12-23', 'Responsabile', 2, '02N120MI', 'Snack e colazione', 'NDRBTE69C13F205G');
INSERT INTO public.impiegati VALUES (55, '2010-12-23', 'Responsabile', 2, '02N120MI', 'Igiene Persona', 'NCCGRD57A08F205D');
INSERT INTO public.impiegati VALUES (56, '2010-12-23', 'Responsabile', 2, '02N120MI', 'Casa e detersivi', 'RSSMRO55T59F205X');
INSERT INTO public.impiegati VALUES (57, '2010-12-23', 'Responsabile', 2, '02N120MI', 'Cancelleria', 'BLLFBR68E11F205P');
INSERT INTO public.impiegati VALUES (58, '2010-12-23', 'Responsabile', 2, '02N120MI', 'Condimenti', 'CLBLVC97T24F205L');
INSERT INTO public.impiegati VALUES (51, '2010-12-23', 'Panettiere', 3, '02N120MI', 'Pane e Pasticceria', 'MLNCCT57E59F205X');
INSERT INTO public.impiegati VALUES (61, '2020-07-26', 'Responsabile', 3, '02N120MI', 'Pane e Pasticceria', 'PGLCLL87D11F205J');
INSERT INTO public.impiegati VALUES (62, '2020-07-26', 'Scaffalista', 5, '02N120MI', 'Pane e Pasticceria', 'LMPPRM64E57F952K');
INSERT INTO public.impiegati VALUES (7, '2020-02-10', 'Scaffalista', 5, '01B109MI', 'Frutta e Verdura', 'PDVNLN94D04F205G');
INSERT INTO public.impiegati VALUES (35, '2010-12-23', 'Responsabile', 2, '01B109MI', 'Frutta e Verdura', 'DVDRNI57T41F205L');
INSERT INTO public.impiegati VALUES (63, '2020-07-27', 'Panettiere', 3, '01B109MI', 'Pane e Pasticceria', 'MRNMLL74C31F205E');
INSERT INTO public.impiegati VALUES (23, '2016-02-14', 'Scaffalista', 5, '02N120MI', 'Frutta e Verdura', 'BCCSLA64B47F205O');
INSERT INTO public.impiegati VALUES (47, '2010-12-23', 'Responsabile', 2, '02N120MI', 'Frutta e Verdura', 'LFNPNC99E12F205E');


--
-- Data for Name: ingredienti; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.ingredienti VALUES (5428255, 5722897, 1);
INSERT INTO public.ingredienti VALUES (5398830, 5722897, 1);
INSERT INTO public.ingredienti VALUES (5383776, 5722897, 1);
INSERT INTO public.ingredienti VALUES (5381668, 5722897, 1);
INSERT INTO public.ingredienti VALUES (5391406, 5722897, 1);
INSERT INTO public.ingredienti VALUES (5400042, 5722897, 1);
INSERT INTO public.ingredienti VALUES (5377561, 5410134, 3);
INSERT INTO public.ingredienti VALUES (5419992, 5410134, 1);
INSERT INTO public.ingredienti VALUES (5409915, 5410134, 1);
INSERT INTO public.ingredienti VALUES (5397815, 5410134, 1);
INSERT INTO public.ingredienti VALUES (5381668, 5410134, 1);
INSERT INTO public.ingredienti VALUES (5574707, 5410134, 1);
INSERT INTO public.ingredienti VALUES (5400042, 5410134, 1);
INSERT INTO public.ingredienti VALUES (5400006, 5735522, 1);
INSERT INTO public.ingredienti VALUES (5385204, 5735522, 2);
INSERT INTO public.ingredienti VALUES (5383211, 5735522, 1);
INSERT INTO public.ingredienti VALUES (5421684, 5735522, 1);
INSERT INTO public.ingredienti VALUES (5382651, 5735522, 1);
INSERT INTO public.ingredienti VALUES (5398830, 5735522, 1);
INSERT INTO public.ingredienti VALUES (5383776, 5735522, 1);
INSERT INTO public.ingredienti VALUES (5381668, 5735522, 1);
INSERT INTO public.ingredienti VALUES (5391406, 5735522, 1);
INSERT INTO public.ingredienti VALUES (5409915, 5383546, 1);
INSERT INTO public.ingredienti VALUES (5397815, 5383546, 1);
INSERT INTO public.ingredienti VALUES (5424211, 5383546, 1);
INSERT INTO public.ingredienti VALUES (5377809, 5383546, 1);
INSERT INTO public.ingredienti VALUES (5391406, 5383546, 1);
INSERT INTO public.ingredienti VALUES (5383546, 5400043, 2);


--
-- Data for Name: metodoPagamento; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public."metodoPagamento" VALUES (1, 'Denaro contante');
INSERT INTO public."metodoPagamento" VALUES (2, 'Carta di credito o prepagata');
INSERT INTO public."metodoPagamento" VALUES (3, 'Bancomat');
INSERT INTO public."metodoPagamento" VALUES (4, 'Bonifico');
INSERT INTO public."metodoPagamento" VALUES (5, 'Assegno bancario');
INSERT INTO public."metodoPagamento" VALUES (6, 'Assegno circolare');
INSERT INTO public."metodoPagamento" VALUES (7, 'MAV');


--
-- Data for Name: orari; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.orari VALUES ('LMMGVSD', '02N120MI', '00:00:00', '23:59:00');
INSERT INTO public.orari VALUES ('SD', '01B109MI', '10:00:00', '19:00:00');
INSERT INTO public.orari VALUES ('LMMGV', '01B109MI', '08:00:00', '20:00:00');


--
-- Data for Name: orariStraordinari; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public."orariStraordinari" VALUES ('01B109MI', '06/01', '10:00:00', '19:00:00');
INSERT INTO public."orariStraordinari" VALUES ('01B109MI', '25/04', '10:00:00', '19:00:00');
INSERT INTO public."orariStraordinari" VALUES ('01B109MI', '01/05', '10:00:00', '19:00:00');
INSERT INTO public."orariStraordinari" VALUES ('01B109MI', '02/06', '10:00:00', '11:00:00');
INSERT INTO public."orariStraordinari" VALUES ('01B109MI', '15/08', '10:00:00', '19:00:00');
INSERT INTO public."orariStraordinari" VALUES ('01B109MI', '01/11', '10:00:00', '19:00:00');
INSERT INTO public."orariStraordinari" VALUES ('01B109MI', '25/12', '00:00:00', '00:00:01');


--
-- Data for Name: ordini; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.ordini VALUES (567, '2020-07-25', 50, 'IT012195608', 5534682, '01B109MI', '2020-08-12', 6);
INSERT INTO public.ordini VALUES (567, '2020-07-14', 45, 'IT012195608', 5534682, '01B109MI', '2020-07-30', 4);
INSERT INTO public.ordini VALUES (745, '2020-07-17', 150, 'IT008203409', 5543413, '01B109MI', NULL, 10);
INSERT INTO public.ordini VALUES (890, '2020-07-27', 45, 'IT010216306', 5837556, '02N120MI', '2020-08-12', 3);


--
-- Data for Name: ordiniPremi; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public."ordiniPremi" VALUES (912461, '23', '2016-12-31', 1);
INSERT INTO public."ordiniPremi" VALUES (912372, '15', '2018-10-09', 3);
INSERT INTO public."ordiniPremi" VALUES (912178, '6', '2020-07-24', 1);
INSERT INTO public."ordiniPremi" VALUES (912330, '11', '2019-05-29', 2);
INSERT INTO public."ordiniPremi" VALUES (903454, '2', '2020-03-18', 1);


--
-- Data for Name: persone; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.persone VALUES ('CTTMLA74B60F205B', 'Amelia', 'Cattaneo', 'Via Sergente Maggiore 103, Milano, MI', '0376 2883161', '1974-02-20', 'AmeliaCattaneo@jourrapide.com');
INSERT INTO public.persone VALUES ('BCCMRT79E20F205D', 'Umberto', 'Buccho', 'Via Santa Maria di Costantinopoli 93, Milano, MI', '0314 6440353', '1979-05-20', 'UmbertoBuccho@jourrapide.com');
INSERT INTO public.persone VALUES ('MNFVRN81P51F205H', 'Venerando', 'Manfrin', 'Strada Statale 149, Milano, MI', '0381 1825320', '1981-09-11', 'VenerandoManfrin@cuvox.de');
INSERT INTO public.persone VALUES ('DNZCMR70B44F205D', 'Calimero', 'Iadanza', 'Corso Porta Nuova 18, Milano, MI', '0371 5379637', '1970-02-04', 'CalimeroIadanza@cuvox.de');
INSERT INTO public.persone VALUES ('CLMLTR67P23F205D', 'Letterio', 'Colombo', 'Corso Casale 43, Milano, MI', '0327 3238410', '1967-09-23', 'LetterioColombo@dayrep.com');
INSERT INTO public.persone VALUES ('CLBNLD61C68F205B', 'Nilde', 'Calabresi', 'Via Nuova Agnano 32, Milano, MI', '0320 9214185', '1961-03-28', 'NildeCalabresi@teleworm.us');
INSERT INTO public.persone VALUES ('CLMPLP88E53F205J', 'Penelope', 'Colombo', 'Via Volto San Luca 119, Milano, MI', '0318 8279142', '1988-05-13', 'PenelopeColombo@dayrep.com');
INSERT INTO public.persone VALUES ('TRVDGN62M51F205F', 'Degna', 'Trevisano', 'Via Solfatara 17, Milano, MI', '0356 5017290', '1962-08-11', 'DegnaTrevisano@jourrapide.com');
INSERT INTO public.persone VALUES ('DNZMRZ93H23F205F', 'Maurizia', 'Iadanza', 'Via Catullo 59, Milano, MI', '0330 2807397', '1993-06-23', 'MauriziaIadanza@jourrapide.com');
INSERT INTO public.persone VALUES ('GRDMLA69H45F205G', 'Amalia', 'Giordano', 'Viale Augusto 46, Milano, MI', '0367 1717515', '1969-06-05', 'AmaliaGiordano@einrot.com');
INSERT INTO public.persone VALUES ('PDVRTL89T06F205I', 'Orestilla', 'Padovano', 'Via Silvio Spaventa 13, Milano, MI', '0381 9537772', '1989-12-06', 'OrestillaPadovano@rhyta.com');
INSERT INTO public.persone VALUES ('PGNLRN67D57F205F', 'Lorna', 'Pagnotto', 'Via Matteo Schilizzi 149, Milano, MI', '0318 4808238', '1967-04-17', 'LornaPagnotto@armyspy.com');
INSERT INTO public.persone VALUES ('MZZFSC64P49F205Z', 'Fosca', 'Mazzanti', 'Via Piave 75, Milano, MI', '0383 2318490', '1964-09-09', 'FoscaMazzanti@dayrep.com');
INSERT INTO public.persone VALUES ('RMNVTR70A23F205M', 'Vittore', 'Romano', 'Via Genova 14, Milano, MI', '0392 4636553', '1970-01-23', 'VittoreRomano@cuvox.de');
INSERT INTO public.persone VALUES ('BCCSLA64B47F205O', 'Ausilia', 'Buccho', 'Via Francesco Del Giudice 144, Milano, MI', '0338 2234649', '1964-02-07', 'AusiliaBuccho@dayrep.com');
INSERT INTO public.persone VALUES ('PLRPLA95A08F205G', 'Paola', 'Palerma', 'Via Scala 114, Milano, MI', '0330 8301836', '1995-01-08', 'PaolaPalerma@armyspy.com');
INSERT INTO public.persone VALUES ('BRSSLS63P20F205M', 'Scolastica', 'Barese', 'Via Belviglieri 6, Milano, MI', '0322 3113403', '1963-09-20', 'ScolasticaBarese@jourrapide.com');
INSERT INTO public.persone VALUES ('NDRRHL96H10F205P', 'Rachele ', 'Endrizzi', 'Via Sedile di Porto 65, Milano, MI', '0315 7406840', '1996-06-10', 'RacheleEndrizzi@jourrapide.com');
INSERT INTO public.persone VALUES ('NPLFST98T57F205H', 'Fausta', 'Napolitano', 'Via delle Coste 108, Milano, MI', '0311 9773504', '1998-12-17', 'FaustaNapolitano@superrito.com');
INSERT INTO public.persone VALUES ('LCCCSP74P57F205F', 'Crispina', 'Lucchesi', 'Via delle Coste 57, Milano, MI', '0367 4060972', '1974-09-17', 'CrispinaLucchesi@superrito.com');
INSERT INTO public.persone VALUES ('PCCCST90S58F205T', 'Cristina', 'Piccio', 'Via Vico Ferrovia 128, Milano, MI', '0378 7415546', '1990-11-18', 'CristinaPiccio@fleckens.hu');
INSERT INTO public.persone VALUES ('GNVPCP76L11F205U', 'Procopio', 'Genovesi', 'Via Santa Maria di Costantinopoli 128, Milano, MI', '0348 7934213', '1976-07-11', 'ProcopioGenovesi@armyspy.com');
INSERT INTO public.persone VALUES ('CLMFSC76B25F205K', 'Fosco', 'Colombo', 'Via Nizza 51, Milano, MI', '0343 9892908', '1976-02-25', 'FoscoColombo@armyspy.com');
INSERT INTO public.persone VALUES ('TRNBNC00R23F205L', 'Bianca', 'Trentini', 'Via dei Serpenti 32, Milano, MI', '0349 6354238', '2000-10-23', 'BiancaTrentini@gustr.com');
INSERT INTO public.persone VALUES ('LMBMLD72B51F205E', 'Imelda', 'Lombardi', 'Via Leopardi 51, Milano, MI', '0320 5448664', '1972-02-11', 'ImeldaLombardi@cuvox.de');
INSERT INTO public.persone VALUES ('DNSLCU98L21F205W', 'Lucio', 'Udinesi', 'Via Pisanelli 142, Milano, MI', '0311 2353959', '1998-07-21', 'LucioUdinesi@superrito.com');
INSERT INTO public.persone VALUES ('PDVNLN94D04F205G', 'Natalina', 'Padovesi', 'Piazza Pilastri 41, Milano, MI', '0393 8981207', '1994-04-04', 'NatalinaPadovesi@superrito.com');
INSERT INTO public.persone VALUES ('FRRLLL58T51F205X', 'Lilla', 'Ferrari', 'Viale delle Province 98, Milano, MI', '0390 3338313', '1958-12-11', 'LillaFerrari@teleworm.us');
INSERT INTO public.persone VALUES ('BRSBLN65L44F205D', 'Abelina', 'Barese', 'Via Santa Lucia 33, Milano, MI', '0381 9235943', '1965-07-04', 'AbelinaBarese@teleworm.us');
INSERT INTO public.persone VALUES ('LMBMNT58E47F205L', 'Amaranto', 'Lombardo', 'Via Corio 105, Milano, MI', '0325 1022062', '1958-05-07', 'AmarantoLombardo@einrot.com');
INSERT INTO public.persone VALUES ('DVDRNI57T41F205L', 'Ireneo', 'Davide', 'Via Giulio Petroni 12, Milano, MI', '0388 3413085', '1957-12-01', 'IreneoDavide@rhyta.com');
INSERT INTO public.persone VALUES ('BCCDRT73M11F205V', 'Dorotea', 'Buccho', 'Via Zannoni 83, Milano, MI', '0397 6012727', '1973-08-11', 'DoroteaBuccho@superrito.com');
INSERT INTO public.persone VALUES ('GRCGTT66H54F205V', 'Gianetto', 'Greece', 'Via Agostino Depretis 83, Milano, MI', '0366 6979224', '1966-06-14', 'GianettoGreece@einrot.com');
INSERT INTO public.persone VALUES ('GLLMRN93D46F205G', 'Marina', 'Gallo', 'Via Nazario Sauro 17, Milano, MI', '0317 8592943', '1993-04-06', 'MarinaGallo@rhyta.com');
INSERT INTO public.persone VALUES ('CNTVRN80T31F205D', 'Veneranda', 'Conti', 'Via Croce Rossa 149, Milano, MI', '0379 2855449', '1980-12-31', 'VenerandaConti@einrot.com');
INSERT INTO public.persone VALUES ('LCCRME59R11F205A', 'Remo', 'Lucchese', 'Via Rosmini 33, Milano, MI', '0336 9600918', '1959-10-11', 'RemoLucchese@jourrapide.com');
INSERT INTO public.persone VALUES ('FRRMTN67T58F205E', 'Martino', 'Ferri', 'Via Castelfidardo 83, Milano, MI', '0371 0516050', '1967-12-18', 'MartinoFerri@cuvox.de');
INSERT INTO public.persone VALUES ('DLLSVT55L69F205L', 'Salvatore', 'Dellucci', 'Via del Piave 108, Milano, MI', '0310 3590146', '1955-07-29', 'SalvatoreDellucci@jourrapide.com');
INSERT INTO public.persone VALUES ('SGSLSE62T23F205Q', 'Elsa', 'Sagese', 'Via Bonafous Alfonso 49, Milano, MI', '0395 1226521', '1962-12-23', 'ElsaSagese@cuvox.de');
INSERT INTO public.persone VALUES ('DLLLVI96D46F205X', 'Livia', 'Dellucci', 'Corso Garibaldi 57, Milano, MI', '0326 8321203', '1996-04-06', 'LiviaDellucci@fleckens.hu');
INSERT INTO public.persone VALUES ('BLLNNF88L04F205M', 'Ninfa', 'Bellucci', 'Via Giberti 55, Milano, MI', '0347 7986675', '1988-07-04', 'NinfaBellucci@teleworm.us');
INSERT INTO public.persone VALUES ('GRCLVE91D07F205B', 'Elvia', 'Greece', 'Piazza San Carlo 116, Milano, MI', '0364 2410500', '1991-04-07', 'ElviaGreece@superrito.com');
INSERT INTO public.persone VALUES ('LFNPNC99E12F205E', 'Principio', 'Li Fonti', 'Via Callicratide 83, Milano, MI', '0372 0231579', '1999-05-12', 'PrincipioLiFonti@teleworm.us');
INSERT INTO public.persone VALUES ('TRVRFN52D12F205F', 'Rufino', 'Trevisan', 'Via Giovanni Amendola 6, Milano, MI', '0327 4320472', '1952-04-12', 'RufinoTrevisan@dayrep.com');
INSERT INTO public.persone VALUES ('PGNSLL68S46F205J', 'Isabella', 'Pagnotto', 'Via Nolana 112, Milano, MI', '0364 8698408', '1968-11-06', 'IsabellaPagnotto@einrot.com');
INSERT INTO public.persone VALUES ('PGNMRN73E16F205A', 'Marina', 'Pagnotto', 'Corso Alcide De Gasperi 80, Milano, MI', '0333 4363510', '1973-05-16', 'MarinaPagnotto@einrot.com');
INSERT INTO public.persone VALUES ('MLNCCT57E59F205X', 'Concetta', 'Milanesi', 'Via Santa Teresa degli Scalzi 54, Milano, MI', '0361 2394119', '1957-05-19', 'ConcettaMilanesi@rhyta.com');
INSERT INTO public.persone VALUES ('MRNRRT65T02F205F', 'Roberto', 'Marino', 'Via Lombardi 107, Milano, MI', '0315 9699980', '1965-12-02', 'RobertoMarino@superrito.com');
INSERT INTO public.persone VALUES ('SBBFDR51A57F205Y', 'Fedro', 'Sabbatini', 'Via Capo le Case 47, Milano, MI', '0355 1133356', '1951-01-17', 'FedroSabbatini@einrot.com');
INSERT INTO public.persone VALUES ('NDRBTE69C13F205G', 'Beato', 'Endrizzi', 'Via Sergente Maggiore 43, Milano, MI', '0320 9919843', '1969-03-13', 'BeatoEndrizzi@dayrep.com');
INSERT INTO public.persone VALUES ('NCCGRD57A08F205D', 'Gerardino', 'Nucci', 'Via dei Fiorentini 15, Milano, MI', '0394 9168546', '1957-01-08', 'GerardinoNucci@teleworm.us');
INSERT INTO public.persone VALUES ('RSSMRO55T59F205X', 'Omero', 'Russo', 'Via Santa Maria di Costantinopoli 119, Milano, MI', '0375 1990309', '1955-12-19', 'OmeroRusso@cuvox.de');
INSERT INTO public.persone VALUES ('BLLFBR68E11F205P', 'Filiberto', 'Bellucci', 'Piazzetta Concordia 110, Milano, MI', '0320 5135729', '1968-05-11', 'FilibertoBellucci@cuvox.de');
INSERT INTO public.persone VALUES ('CLBLVC97T24F205L', 'Lodovico', 'Calabrese', 'Via Colonnello Galliano 84, Milano, MI', '0317 1401597', '1997-12-24', 'LodovicoCalabrese@jourrapide.com');
INSERT INTO public.persone VALUES ('NGLDND73S59F205H', 'Edmondo', 'Angelo', 'Piazza Giuseppe Garibaldi 38, Milano, MI', '0343 8742864', '1973-11-19', 'EdmondoAngelo@dayrep.com');
INSERT INTO public.persone VALUES ('CSTLNS83S13F205V', 'Alfonsino', 'Costa', 'Piazza Bovio 139, Milano, MI', '0363 2033019', '1983-11-13', 'AlfonsinoCosta@dayrep.com');
INSERT INTO public.persone VALUES ('PSNNLC00M70F205G', 'Angelico', 'Pisani', 'Via Sergente Maggiore 99, Milano, MI', '0326 8259676', '2000-08-30', 'AngelicoPisani@fleckens.hu');
INSERT INTO public.persone VALUES ('PLRPTL00P61F205R', 'Pantaleone', 'Palerma', 'Via Santa Maria di Costantinopoli 64, Milano, MI', '0324 3423920', '2000-09-21', 'PantaleonePalerma@dayrep.com');
INSERT INTO public.persone VALUES ('CCCGGR50B09F205D', 'Gregorio', 'Cocci', 'Via Medina 120, Milano, MI', '0326 6078148', '1950-02-09', 'GregorioCocci@armyspy.com');
INSERT INTO public.persone VALUES ('MNLVND74P05F205J', 'Violanda', 'Monaldo', 'Corso Porta Borsari 37, Milano, MI', '0347 6023681', '1974-09-05', 'ViolandaMonaldo@cuvox.de');
INSERT INTO public.persone VALUES ('TRVRSL55M55F205C', 'Rosalia', 'Trevisano', 'Corso Porta Nuova 72, Milano, MI', '0331 5138178', '1955-08-15', 'RosaliaTrevisano@cuvox.de');
INSERT INTO public.persone VALUES ('LGGGMN58L24F205R', 'Germano', 'Loggia', 'Via Silvio Spaventa 116, Milano, MI', '0386 3715164', '1958-07-24', 'GermanoLoggia@einrot.com');
INSERT INTO public.persone VALUES ('CTTLNZ48C62F205U', 'Lorenzo', 'Cattaneo', 'Piazza Mercato 43, Milano, MI', '0373 8876518', '1948-03-22', 'LorenzoCattaneo@einrot.com');
INSERT INTO public.persone VALUES ('MRNMLL74C31F205E', 'Mirella', 'Marino', 'Via del Mascherone 61, Milano, MI', '0317 8389493', '1974-03-31', 'MirellaMarino@dayrep.com');
INSERT INTO public.persone VALUES ('PGLCLL87D11F205J', 'Cirilla', 'Pugliesi', 'Via Zannoni 18, Milano, MI', '0379 9105115', '1987-04-11', 'CirillaPugliesi@einrot.com');
INSERT INTO public.persone VALUES ('LMPPRM64E57F952K', 'Olimpia', 'Palerma', 'Via Miguel de Cervantes, 32, Milano, MI', '0397 5868630', '1976-05-17', 'OlimpiaPalerma@teleworm.us');


--
-- Data for Name: premi; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.premi VALUES (912819, 'La crotta di vegneron due bottiglie', 'Tour del gusto', 1700, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912810, 'Cofanetto biscotti e moscato', 'Tour del gusto', 4300, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912816, 'Ferrari due bottiglie e stopper', 'Tour del gusto', 7700, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912807, 'F.lli beretta cofanetto due salami dop', 'Tour del gusto', 3800, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912805, 'Marco felluga confezione tre bottiglie', 'Tour del gusto', 3500, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912820, 'Bertani confezione tre bottiglie', 'Tour del gusto', 5900, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912804, 'Cav. boschi confezione tre salumi', 'Tour del gusto', 5900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912743, 'Cofanetto frantoio venturino', 'Tour del gusto', 2000, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912817, 'Cofanetto prodotti tipici', 'Tour del gusto', 3600, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912818, 'Coricelli dama olio dop', 'Tour del gusto', 2700, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912808, 'Spinosi sei confezioni di paste diverse', 'Tour del gusto', 1900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912802, 'Collefrisio confezione tre bottiglie', 'Tour del gusto', 1600, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912806, 'Cofanetto prodotti tipici', 'Tour del gusto', 3500, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912809, 'La molisana confezione extra lusso', 'Tour del gusto', 1000, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912744, 'Latta pastificio gentile', 'Tour del gusto', 3300, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912812, 'Spagnoletti zeuli dama di olio dop', 'Tour del gusto', 2500, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912803, 'San vincenzo confezione quattro salumi', 'Tour del gusto', 3300, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912801, 'Paternoster confezione due bottiglie', 'Tour del gusto', 3100, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912815, 'Damiano confezione dieci prodotti', 'Tour del gusto', 4200, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912814, 'Dolianova confezione tre bottiglie', 'Tour del gusto', 2300, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912230, '6 bicchieri', 'Tavola e cucina', 13900, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912424, 'Brocca', 'Tavola e cucina', 2300, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912369, 'Tovaglia antimacchia', 'Tavola e cucina', 3000, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912556, '6 bicchieri acqua', 'Tavola e cucina', 2400, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912461, 'Set coppe multiuso', 'Tavola e cucina', 1400, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912584, 'Posate insalata', 'Tavola e cucina', 1500, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912555, '2 bicchieri birra', 'Tavola e cucina', 2600, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912557, 'Alzatina scomponibile', 'Tavola e cucina', 2400, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912735, 'Cassetta 6 vini', 'Tavola e cucina', 19900, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912741, 'Gin', 'Tavola e cucina', 2800, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912214, 'Estrattore', 'Tavola e cucina', 26900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912537, 'Set macedonia', 'Tavola e cucina', 2400, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912534, 'Coperchi estensibili', 'Tavola e cucina', 3400, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912514, 'Grattugia', 'Tavola e cucina', 1300, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912553, 'Padella quadrata', 'Tavola e cucina', 4900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912517, 'Macinapepe', 'Tavola e cucina', 2400, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912472, 'Mini griglia', 'Tavola e cucina', 1800, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912420, 'Padella ferro', 'Tavola e cucina', 4100, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912742, 'Dama olio extravergine 100% italiano', 'Tavola e cucina', 2900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912399, 'Friggitrice', 'Tavola e cucina', 8400, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912518, 'Veggie sheet slicer', 'Tavola e cucina', 3000, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912402, 'Tritatutto', 'Tavola e cucina', 4000, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912397, 'Macchina caffe espresso', 'Tavola e cucina', 9600, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912599, 'Affilacoltelli', 'Tavola e cucina', 2200, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912229, 'Ceppo coltelli', 'Tavola e cucina', 36900, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912503, 'Casseruola', 'Tavola e cucina', 6000, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912220, 'Macchina da caffè automatica', 'Tavola e cucina', 35900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912217, 'Affettatrice elettrica', 'Tavola e cucina', 59900, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912683, 'Orologio a parete', 'Tavola e cucina', 7100, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912363, 'Tappeto', 'Arredo e cura della casa', 5800, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912400, 'Grill', 'Tavola e cucina', 10900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912401, 'Tostafette', 'Tavola e cucina', 6900, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912398, 'Frullatore', 'Tavola e cucina', 11900, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912225, 'Robot da cucina', 'Tavola e cucina', 44900, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912541, 'Bottiglia', 'Tavola e cucina', 2500, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912440, 'Bicchierini', 'Tavola e cucina', 3500, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912734, 'Contenitore raccolta differenziata', 'Tavola e cucina', 9900, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912192, 'Samsung frigorifero combinato', 'Tavola e cucina', 69900, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912738, 'Vaso', 'Arredo e cura della casa', 9900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912682, 'Portariviste', 'Arredo e cura della casa', 9900, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912684, 'Lampada', 'Arredo e cura della casa', 8900, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912372, 'Cuscino arredo', 'Arredo e cura della casa', 5200, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912182, 'Completo copripiumino matrimoniale', 'Arredo e cura della casa', 10900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912178, 'Piumino singolo', 'Arredo e cura della casa', 17900, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912368, 'Quilt matrimoniale', 'Arredo e cura della casa', 14600, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912367, 'Completo letto matrimoniale', 'Arredo e cura della casa', 10800, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912685, 'Lampada da tavolo', 'Arredo e cura della casa', 17900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912653, 'Pouf contenitore', 'Arredo e cura della casa', 3400, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912324, 'Kit striscia led 10 metri rgb wifi', 'Arredo e cura della casa', 4700, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912737, 'Robot aspirapolvere', 'Arredo e cura della casa', 59900, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912224, 'Aspirapolvere senza filo', 'Arredo e cura della casa', 20900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912223, 'Caldaia e stiro verticale', 'Arredo e cura della casa', 21400, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912204, 'Tv 49''''', 'Stile e tecno', 84900, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912395, 'Cuffia', 'Stile e tecno', 15900, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912390, 'Giradischi', 'Stile e tecno', 17900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912211, 'Diffusore wireless e bluetooth', 'Stile e tecno', 73900, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912184, 'Partybox', 'Stile e tecno', 23400, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912388, 'Gaming kit', 'Stile e tecno', 11600, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912205, 'Radio fm/dab+ e speaker bluetooth', 'Stile e tecno', 20900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912391, 'Rialzo per monitor multiporta', 'Stile e tecno', 6500, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912686, 'Tracolla', 'Stile e tecno', 5900, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912732, 'Porta documenti', 'Stile e tecno', 2400, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912692, 'Bracciale viola provenza', 'Stile e tecno', 3700, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912693, 'Bracciale arcobaleno dorato', 'Stile e tecno', 3800, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912690, 'Orecchini', 'Stile e tecno', 2700, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912694, 'Occhiali da sole', 'Stile e tecno', 4700, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912688, 'Orologio', 'Stile e tecno', 3000, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912719, 'Set regalo penne', 'Stile e tecno', 2700, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912394, 'Supporto smartphone', 'Stile e tecno', 1000, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912325, 'Piastra vapore', 'Bellezza e salute', 5400, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912329, 'Asciugacapelli', 'Bellezza e salute', 4100, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912371, 'Biancheria bagno', 'Bellezza e salute', 2200, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912337, 'Elettro stimolatore', 'Bellezza e salute', 4400, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912327, 'Rasoio', 'Bellezza e salute', 7400, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912330, 'Regolabarba tagliacapelli', 'Bellezza e salute', 2900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912331, 'Grooming kit', 'Bellezza e salute', 5200, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912334, 'Misuratore di pressione da polso', 'Bellezza e salute', 9700, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912247, 'Calciobalilla', 'Tempo libero', 42900, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912393, 'Braccialetto fitness', 'Tempo libero', 13400, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912212, 'Monopattino elettrico', 'Tempo libero', 37900, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912250, 'Mountain bike modello nava lady', 'Tempo libero', 37900, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912249, 'Mountain bike modello giovi', 'Tempo libero', 31900, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912177, 'Airbag per motociclisti', 'Tempo libero', 23900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912300, 'Casco mini jet', 'Tempo libero', 7900, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912241, 'Valigia spinner 55', 'Tempo libero', 23900, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912242, 'Valigia spinner 69', 'Tempo libero', 25900, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912197, 'Action camera', 'Tempo libero', 47900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912396, 'Auricolare', 'Tempo libero', 9900, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912244, 'Tapis roulant', 'Tempo libero', 37900, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912392, 'Smartwatch', 'Tempo libero', 18900, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912718, 'Torcia ricaricabile', 'Tempo libero', 2800, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912639, 'Coltello multiuso', 'Tempo libero', 6400, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912231, 'Barbecue a carbone', 'Tempo libero', 18900, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912174, 'Sedia dondolo con due cuscini', 'Tempo libero', 19900, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912287, 'Libro le meraviglie del mondo', 'Tempo libero', 1900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912299, 'Kit fiorera con sottovaso e ciotola', 'Tempo libero', 4400, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912173, 'Set irrigatore', 'Tempo libero', 21400, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912311, 'Sedia sdraio da giardino', 'Tempo libero', 7300, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912321, 'Rasabordi con due batterie', 'Tempo libero', 11400, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912407, 'Cuscinetto antiabbandono', 'Bimbi', 5500, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912228, 'Seggiolino auto con cuscinetto antiabbandono', 'Bimbi', 17900, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912720, 'Domino morbido', 'Bimbi', 1600, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912722, 'Bambola con accessori', 'Bimbi', 3300, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912723, 'Gioco a incastro trainabile', 'Bimbi', 2300, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912725, 'Raccontastorie', 'Bimbi', 5000, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912259, 'Bici senza pedali', 'Bimbi', 10900, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912724, 'Pasta modellabile', 'Bimbi', 2400, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912727, 'Supermercato portatile', 'Bimbi', 3700, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912729, 'Dinosauri con caverna', 'Bimbi', 4800, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912726, 'Gioco di coding', 'Bimbi', 1600, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912728, 'Set colazione da dipingere', 'Bimbi', 2000, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912698, 'Occhiali da sole', 'Bimbi', 4500, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912731, 'Gioco da tavolo', 'Bimbi', 2900, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912296, 'Le grandi enciclopedie', 'Bimbi', 3900, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912240, 'Acquario', 'Animali', 25900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (912678, 'Ciotola e sottociotola cane', 'Animali', 3000, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (912672, 'Borsa trasportino gatto', 'Animali', 6400, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (912677, 'Kit viaggio gatto', 'Animali', 3900, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (912679, 'Kit viaggio cane', 'Animali', 3900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (903418, 'Visita con degustazione michele chiarlo', 'Idee', 2300, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (903420, 'Visita con degustazione il bosco', 'Idee', 1600, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (903416, 'Visita con degustazione guido berlucchi', 'Idee', 2900, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (903415, 'Visita con degustazione masi', 'Idee', 2900, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (903419, 'Visita con degustazione umberto cesari', 'Idee', 2900, '2019-10-11', '2020-10-10');
INSERT INTO public.premi VALUES (903458, 'Visita con degustazione castello di volpaia', 'Idee', 2600, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (903451, 'Visita con degustazione la braccesca', 'Idee', 2300, '2017-10-11', '2018-10-10');
INSERT INTO public.premi VALUES (903452, 'Visita con degustazione le mortelle', 'Idee', 2300, '2018-10-11', '2019-10-10');
INSERT INTO public.premi VALUES (903453, 'Sconto del 20% sulla spesa', 'Sconti', 400, '2016-10-11', '2017-10-10');
INSERT INTO public.premi VALUES (903454, 'Sconto del 30% sulla spesa', 'Sconti', 500, '2017-10-11', '2020-10-10');


--
-- Data for Name: premiSpeciali; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public."premiSpeciali" VALUES (912217, 29900, 100);
INSERT INTO public."premiSpeciali" VALUES (912225, 20000, 150);
INSERT INTO public."premiSpeciali" VALUES (912737, 39900, 200);
INSERT INTO public."premiSpeciali" VALUES (912204, 40000, 299.99);
INSERT INTO public."premiSpeciali" VALUES (912211, 39500, 55);
INSERT INTO public."premiSpeciali" VALUES (912212, 10000, 68.5);


--
-- Data for Name: prenotazioni; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.prenotazioni VALUES (912212, '02N120MI', '10', '2020-07-29 10:28:03', 'punti', 1, 'AAAA', true);
INSERT INTO public.prenotazioni VALUES (912225, '02N120MI', '10', '2019-07-29 10:29:07', 'punti', 2, 'AAAB', false);
INSERT INTO public.prenotazioni VALUES (912204, '02N120MI', '10', '2020-07-29 10:31:08', 'punti', 1, 'AAAC', false);
INSERT INTO public.prenotazioni VALUES (912212, '02N120MI', '11', '2020-07-29 10:28:03', 'mista', 1, 'AAAD', false);
INSERT INTO public.prenotazioni VALUES (912225, '02N120MI', '11', '2019-07-29 10:29:07', 'punti', 2, 'AAAE', true);
INSERT INTO public.prenotazioni VALUES (912204, '02N120MI', '11', '2020-07-29 10:31:08', 'punti', 2, 'AAAF', false);


--
-- Data for Name: prodotti; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.prodotti VALUES (5722897, 'Esselunga, insalata di pollo ', 13.99, 'Insalate', true, 60);
INSERT INTO public.prodotti VALUES (5410134, 'Esselunga 4 Croissant alla marmellata 300 g', 3.79, 'Pasticceria', true, 160);
INSERT INTO public.prodotti VALUES (5735522, 'Esselunga, Sushi mix 4 porzioni 860 g', 25.5, 'Sushi', true, 120);
INSERT INTO public.prodotti VALUES (5383546, 'Focaccine ', 12.9, 'Panetteria', true, 110);
INSERT INTO public.prodotti VALUES (5400043, 'Focaccine Doppie', 25, 'Panetteria', true, 10);
INSERT INTO public.prodotti VALUES (5382198, 'Esselunga Bio, kiwi biologici ', 4.98, 'Frutta fresca', false, 50);
INSERT INTO public.prodotti VALUES (5385170, 'Esselunga Bio, meloni biologici ', 2.84, 'Frutta fresca', false, 120);
INSERT INTO public.prodotti VALUES (5381634, 'Lattuga Iceberg, ', 1.68, 'Insalate e radicchi', false, 140);
INSERT INTO public.prodotti VALUES (5391756, 'Esselunga Bio, peperoni rossi biologici ', 4.98, 'Verdura fresca', false, 190);
INSERT INTO public.prodotti VALUES (5837556, 'Esselunga, carciofi al vapore 250 g', 2.88, 'Verdura cotta', false, 170);
INSERT INTO public.prodotti VALUES (5386477, 'Esselunga, filetto di platessa ', 19.9, 'Pesce', false, 100);
INSERT INTO public.prodotti VALUES (5772507, 'Smart, Fish Burger salmone 240 g', 3.59, 'Fishburger', false, 70);
INSERT INTO public.prodotti VALUES (5745336, 'Esselunga Hamburgeria, bovino adulto di razza piemontese hamburger classici 250 g', 4.49, 'Hamburgeria', false, 80);
INSERT INTO public.prodotti VALUES (5534682, 'Amadori, hamburger ricetta classica 204 g', 2.89, 'Hamburgeria', false, 180);
INSERT INTO public.prodotti VALUES (5428255, 'Esselunga Naturama, petto di pollo a fette allevato senza uso di antibiotici ', 12.85, 'Pollame', false, 100);
INSERT INTO public.prodotti VALUES (5388593, 'Esselunga, pollo durelli ', 3.95, 'Pollame', false, 70);
INSERT INTO public.prodotti VALUES (5419992, 'Esselunga Top, burro 100% italiano 250 g', 2.69, 'Burro', false, 150);
INSERT INTO public.prodotti VALUES (5377561, 'Esselunga Bio, 6 uova fresche biologiche', 2.19, 'Uova', false, 120);
INSERT INTO public.prodotti VALUES (5520562, 'Aia, Pasta gialla 6 uova fresche medie italiane allevate a terra', 2.45, 'Uova', false, 90);
INSERT INTO public.prodotti VALUES (5561086, 'Esselunga Equilibrio, grissinetti integrali conf. 5x50 g', 1.29, 'Grissini', false, 60);
INSERT INTO public.prodotti VALUES (5527406, 'Esselunga Top, lasagne sfoglia rustica 250 g', 1.29, 'Pasta fresca', false, 180);
INSERT INTO public.prodotti VALUES (5515756, 'Esselunga, pasta sfoglia rettangolare 230 g', 1.29, 'Pasta fresca', false, 130);
INSERT INTO public.prodotti VALUES (5731069, 'Cucciolone, l''Originale classico 6 pezzi 480 g', 3.29, 'Gelati', false, 80);
INSERT INTO public.prodotti VALUES (5386365, 'Esselunga Bio, pisellini primizia biologiche surgelati 750 g', 2.79, 'Verdura', false, 70);
INSERT INTO public.prodotti VALUES (5398136, 'Cameo, Pizza Regina Alta Margherita surgelata 370 g', 2.45, 'Pizze e torte salate', false, 80);
INSERT INTO public.prodotti VALUES (5389264, 'Kinder, Bueno white conf. 3x39 g', 1.99, 'Snack dolci', false, 60);
INSERT INTO public.prodotti VALUES (5714217, 'Popz, Microwave popcorn salati conf. 4x85 g', 3.68, 'Patatine e Snack salati', false, 170);
INSERT INTO public.prodotti VALUES (5513931, 'Esselunga, Crostatine alla fragola 8 pezzi 368 g', 1.74, 'Merendine', false, 170);
INSERT INTO public.prodotti VALUES (5641023, 'Mentadent, Vertical Expert Sensitive spazzolino extra soft', 2.49, 'Spazzolini', false, 80);
INSERT INTO public.prodotti VALUES (5402283, 'Sunsilk, Liscio perfetto shampoo 400 ml', 3.5, 'Cura Capelli', false, 170);
INSERT INTO public.prodotti VALUES (5543413, 'Gillette, Blue3 rasoio 3 lame usa e getta 12 pezzi + 4 omaggio', 8.9, 'Rasatura e depilazione', false, 180);
INSERT INTO public.prodotti VALUES (5392341, 'Ace, candeggina classica 4 l', 2.39, 'Detersivi e pulizia casa', false, 70);
INSERT INTO public.prodotti VALUES (5384648, 'Vulcano, Spirali Vulcano extra 10 pezzi', 1.65, 'Giardinaggio ed insetticidi', false, 190);
INSERT INTO public.prodotti VALUES (5396751, 'Foxy, Cartapaglia asciugatutto 2 rotoloni', 2.15, 'Carta e monouso', false, 160);
INSERT INTO public.prodotti VALUES (5388822, 'Monocromo Quaderno maxi formato A4, rigatura 1R senza margini, 100 gr, colori assortiti', 2.59, 'Quaderni', false, 160);
INSERT INTO public.prodotti VALUES (5402665, 'Staedtler Ball 432 Penne a sfera colorate, 10 pezzi', 4.89, 'Penne a Sfera e Gel', false, 50);
INSERT INTO public.prodotti VALUES (5402618, 'Giotto Acquerelli in astuccio con pennello, 12 colori', 3.09, 'Tempere e Album da disegno', false, 50);
INSERT INTO public.prodotti VALUES (5400006, 'Esselunga, riso Vialone Nano 1 kg', 2.49, 'Riso bianco', false, 70);
INSERT INTO public.prodotti VALUES (5385204, 'Cetrioli, ', 1.3, 'Altre verdure', false, 80);
INSERT INTO public.prodotti VALUES (5383211, 'Esselunga, tonno all''olio d''oliva conf. 4x160 g', 6.99, 'Tonno in olio d''oliva', false, 90);
INSERT INTO public.prodotti VALUES (5421684, 'Cerreto, I Semi semi di zucca biologici 200 g', 4.48, 'Semi e condimenti', false, 80);
INSERT INTO public.prodotti VALUES (5398830, 'Esselunga, maionese 500 ml', 1.57, 'Maionese', false, 190);
INSERT INTO public.prodotti VALUES (5383776, 'Esselunga Bio, aceto di mele biologico 75 cl', 2.59, 'Aceto e limone', false, 110);
INSERT INTO public.prodotti VALUES (5381668, 'Eridania, zucchero bianco 1 kg', 0.89, 'Zucchero', false, 150);
INSERT INTO public.prodotti VALUES (5391406, 'Esselunga, sale iodato fino 1 kg', 0.5, 'Sale', false, 150);
INSERT INTO public.prodotti VALUES (5409915, 'Esselunga Bio, farina tipo 00 di grano tenero biologica 1 kg', 1.09, 'Farina', false, 80);
INSERT INTO public.prodotti VALUES (5397815, 'Esselunga, Dolomiti naturale conf. 6x1,5 l', 1.74, 'Acqua naturale', false, 200);
INSERT INTO public.prodotti VALUES (5424211, 'Esselunga Bio, olio extra vergine di oliva 100% italiano 750 ml', 9.49, 'Olio extra-vergine', false, 170);
INSERT INTO public.prodotti VALUES (5377809, 'Lievital, lievito fresco per pizza conf. 2x25 g', 0.28, 'Lieviti', false, 60);
INSERT INTO public.prodotti VALUES (5574707, 'Esselunga, latte fresco intero 100% italiano alta qualitÃ  1 l', 1.19, 'Latte fresco', false, 70);
INSERT INTO public.prodotti VALUES (5382651, 'Esselunga, filetto di salmone al Kg', 24.15, 'Pesce', false, 80);
INSERT INTO public.prodotti VALUES (5429355, 'Esselunga Bio, confettura extra di albicocche biologica 340 g', 2.59, 'Marmellate', false, 50);
INSERT INTO public.prodotti VALUES (5400042, 'Sedano verde, ', 1.68, 'Altre verdure', false, 120);


--
-- Data for Name: punti; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.punti VALUES (5421684, '2019-07-12', '2020-07-12', 400);
INSERT INTO public.punti VALUES (5388822, '2020-09-14', '2021-09-14', 30);
INSERT INTO public.punti VALUES (5543413, '2020-07-14', '2021-07-19', 100);
INSERT INTO public.punti VALUES (5386365, '2019-09-15', '2020-09-16', 250);
INSERT INTO public.punti VALUES (5745336, '2018-07-13', '2019-07-12', 150);
INSERT INTO public.punti VALUES (5735522, '2019-10-19', '2020-10-31', 300);
INSERT INTO public.punti VALUES (5389264, '2020-07-16', '2020-07-26', 24);


--
-- Data for Name: reparti; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.reparti VALUES ('01B109MI', 'Pesce e Sushi');
INSERT INTO public.reparti VALUES ('01B109MI', 'Macelleria');
INSERT INTO public.reparti VALUES ('01B109MI', 'Latticini');
INSERT INTO public.reparti VALUES ('01B109MI', 'Pane e Pasticceria');
INSERT INTO public.reparti VALUES ('01B109MI', 'Gastronomia');
INSERT INTO public.reparti VALUES ('01B109MI', 'Surgelati e gelati');
INSERT INTO public.reparti VALUES ('01B109MI', 'Snack e colazione');
INSERT INTO public.reparti VALUES ('01B109MI', 'Igiene Persona');
INSERT INTO public.reparti VALUES ('01B109MI', 'Casa e detersivi');
INSERT INTO public.reparti VALUES ('01B109MI', 'Cancelleria');
INSERT INTO public.reparti VALUES ('01B109MI', 'Condimenti');
INSERT INTO public.reparti VALUES ('02N120MI', 'Pesce e Sushi');
INSERT INTO public.reparti VALUES ('02N120MI', 'Macelleria');
INSERT INTO public.reparti VALUES ('02N120MI', 'Latticini');
INSERT INTO public.reparti VALUES ('02N120MI', 'Pane e Pasticceria');
INSERT INTO public.reparti VALUES ('02N120MI', 'Gastronomia');
INSERT INTO public.reparti VALUES ('02N120MI', 'Surgelati e gelati');
INSERT INTO public.reparti VALUES ('02N120MI', 'Snack e colazione');
INSERT INTO public.reparti VALUES ('02N120MI', 'Igiene Persona');
INSERT INTO public.reparti VALUES ('02N120MI', 'Casa e detersivi');
INSERT INTO public.reparti VALUES ('02N120MI', 'Cancelleria');
INSERT INTO public.reparti VALUES ('02N120MI', 'Condimenti');
INSERT INTO public.reparti VALUES ('01B109MI', 'Magazzino');
INSERT INTO public.reparti VALUES ('02N120MI', 'Magazzino');
INSERT INTO public.reparti VALUES ('02N120MI', 'Pasta e riso');
INSERT INTO public.reparti VALUES ('01B109MI', 'Bibite, Acqua e Alchol');
INSERT INTO public.reparti VALUES ('02N120MI', 'Bibite, Acqua e Alchol ');
INSERT INTO public.reparti VALUES ('01B109MI', 'Cosmetici');
INSERT INTO public.reparti VALUES ('01B109MI', 'Frutta e Verdura');
INSERT INTO public.reparti VALUES ('02N120MI', 'Frutta e Verdura');
INSERT INTO public.reparti VALUES ('02N120MI', 'Cosmetici');
INSERT INTO public.reparti VALUES ('01B109MI', 'Pasta e riso');


--
-- Data for Name: scontrini; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.scontrini VALUES (278987, '2020-05-12', 2, 2, '01B109MI', '1', NULL);
INSERT INTO public.scontrini VALUES (267865, '2020-05-12', 4, 1, '02N120MI', NULL, NULL);


--
-- Data for Name: scortePremi; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: supermercati; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.supermercati VALUES ('01B109MI', 350, 'Via Bologna, 109, Milano, MI');
INSERT INTO public.supermercati VALUES ('02N120MI', 600, 'Via Nicolai, 120, Milano, MI');


--
-- Data for Name: turni; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.turni VALUES (37, 'Sabato', '10:00:00', '15:00:00');
INSERT INTO public.turni VALUES (9, 'Domenica', '10:00:00', '15:00:00');
INSERT INTO public.turni VALUES (37, 'Domenica', '15:00:00', '19:00:00');
INSERT INTO public.turni VALUES (9, 'Sabato', '15:00:00', '19:00:00');
INSERT INTO public.turni VALUES (37, 'Giovedi', '08:00:00', '15:00:00');
INSERT INTO public.turni VALUES (9, 'Lunedi', '08:00:00', '15:00:00');
INSERT INTO public.turni VALUES (37, 'Martedi', '08:00:00', '15:00:00');
INSERT INTO public.turni VALUES (9, 'Giovedi', '15:00:00', '20:00:00');
INSERT INTO public.turni VALUES (9, 'Venerdi', '08:00:00', '15:00:00');
INSERT INTO public.turni VALUES (9, 'Martedi', '15:00:00', '20:00:00');
INSERT INTO public.turni VALUES (37, 'Mercoledi', '15:00:00', '20:00:00');
INSERT INTO public.turni VALUES (9, 'Mercoledi', '08:00:00', '15:00:00');
INSERT INTO public.turni VALUES (37, 'Lunedi', '15:00:00', '20:00:00');
INSERT INTO public.turni VALUES (37, 'Venerdi', '15:00:00', '20:00:00');


--
-- Data for Name: vendite; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.vendite VALUES (278987, 5745336, 3, 4.49);
INSERT INTO public.vendite VALUES (278987, 5402283, 1, 1.25);
INSERT INTO public.vendite VALUES (278987, 5513931, 4, 1.74);
INSERT INTO public.vendite VALUES (267865, 5388593, 2, 3.95);
INSERT INTO public.vendite VALUES (267865, 5534682, 1, 2.89);
INSERT INTO public.vendite VALUES (267865, 5389264, 3, 1.99);


--
-- Name: impiegati_matricola_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.impiegati_matricola_seq', 63, true);


--
-- Name: metodoPagamento_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public."metodoPagamento_id_seq"', 7, true);


--
-- Name: casse casse_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.casse
    ADD CONSTRAINT casse_pk PRIMARY KEY (supermercato, "nCassa");


--
-- Name: cassieri cassieri_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cassieri
    ADD CONSTRAINT cassieri_pk PRIMARY KEY (impiegato, "supermercatoCassa", "nCassa");


--
-- Name: clienti clienti_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clienti
    ADD CONSTRAINT clienti_pk PRIMARY KEY ("nTessera");


--
-- Name: contenutoReparto contenutoreparto_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."contenutoReparto"
    ADD CONSTRAINT contenutoreparto_pk PRIMARY KEY ("idBatch");


--
-- Name: fornitori fornitore_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fornitori
    ADD CONSTRAINT fornitore_pk PRIMARY KEY (iva);


--
-- Name: impiegati impiegati_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.impiegati
    ADD CONSTRAINT impiegati_pk PRIMARY KEY (matricola);


--
-- Name: metodoPagamento metodopagamento_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."metodoPagamento"
    ADD CONSTRAINT metodopagamento_pk PRIMARY KEY (id);


--
-- Name: orari orari_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orari
    ADD CONSTRAINT orari_pk PRIMARY KEY (giorni, supermercato);


--
-- Name: orariStraordinari oraristraordinari_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."orariStraordinari"
    ADD CONSTRAINT oraristraordinari_pk PRIMARY KEY (supermercato, data);


--
-- Name: ordiniPremi ordinepremi_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."ordiniPremi"
    ADD CONSTRAINT ordinepremi_pk PRIMARY KEY (premio, cliente, "dataOrdine");


--
-- Name: ordini ordini_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ordini
    ADD CONSTRAINT ordini_pk PRIMARY KEY ("dataOrdine", fornitore, prodotto, supermercato);


--
-- Name: persone persone_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.persone
    ADD CONSTRAINT persone_pk PRIMARY KEY ("codF");


--
-- Name: premi premi_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.premi
    ADD CONSTRAINT premi_pk PRIMARY KEY (codice);


--
-- Name: premiSpeciali premispeciali_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."premiSpeciali"
    ADD CONSTRAINT premispeciali_pk PRIMARY KEY (premio);


--
-- Name: prenotazioni prenotazioni_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prenotazioni
    ADD CONSTRAINT prenotazioni_pk PRIMARY KEY (codice);


--
-- Name: prodotti prodotti_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prodotti
    ADD CONSTRAINT prodotti_pk PRIMARY KEY (id);


--
-- Name: punti punti_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.punti
    ADD CONSTRAINT punti_pk PRIMARY KEY (prodotto, "dataInserimento");


--
-- Name: reparti reparti_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reparti
    ADD CONSTRAINT reparti_pk PRIMARY KEY (supermercato, nome);


--
-- Name: scontrini scontrini_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.scontrini
    ADD CONSTRAINT scontrini_pk PRIMARY KEY (id);


--
-- Name: scortePremi scortepremi_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."scortePremi"
    ADD CONSTRAINT scortepremi_pk PRIMARY KEY (supermercato, premio);


--
-- Name: supermercati supermercato_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.supermercati
    ADD CONSTRAINT supermercato_pk PRIMARY KEY (codice);


--
-- Name: ingredienti table_name_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ingredienti
    ADD CONSTRAINT table_name_pk PRIMARY KEY (ingrediente, "prodottoFinale");


--
-- Name: turni turni_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.turni
    ADD CONSTRAINT turni_pk PRIMARY KEY (impiegato, giorno);


--
-- Name: vendite vendite_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendite
    ADD CONSTRAINT vendite_pk PRIMARY KEY (scontrino, prodotto);


--
-- Name: clienti_persona_uindex; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX clienti_persona_uindex ON public.clienti USING btree (persona);


--
-- Name: contenutoreparto_prodotto_datascadenza_nomereparto_supermercato; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX contenutoreparto_prodotto_datascadenza_nomereparto_supermercato ON public."contenutoReparto" USING btree (prodotto, "dataScadenza", "nomeReparto", "supermercatoReparto");


--
-- Name: impiegati_persona_uindex; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX impiegati_persona_uindex ON public.impiegati USING btree (persona);


--
-- Name: persone_mail_uindex; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX persone_mail_uindex ON public.persone USING btree (mail);


--
-- Name: prenotazioni_premiospeciale_cliente_timestampordine_uindex; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX prenotazioni_premiospeciale_cliente_timestampordine_uindex ON public.prenotazioni USING btree ("premioSpeciale", cliente, "timestampOrdine");


--
-- Name: casse casse_supermercato_indirizzo_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.casse
    ADD CONSTRAINT casse_supermercato_indirizzo_fk FOREIGN KEY (supermercato) REFERENCES public.supermercati(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: cassieri cassieri_casse_supermercato_ncassa_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cassieri
    ADD CONSTRAINT cassieri_casse_supermercato_ncassa_fk FOREIGN KEY ("supermercatoCassa", "nCassa") REFERENCES public.casse(supermercato, "nCassa") ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: cassieri cassieri_impiegati_matricola_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cassieri
    ADD CONSTRAINT cassieri_impiegati_matricola_fk FOREIGN KEY (impiegato) REFERENCES public.impiegati(matricola) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: clienti clienti_persone_codf_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clienti
    ADD CONSTRAINT clienti_persone_codf_fk FOREIGN KEY (persona) REFERENCES public.persone("codF") ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: contenutoReparto contenutoreparto_prodotti_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."contenutoReparto"
    ADD CONSTRAINT contenutoreparto_prodotti_id_fk FOREIGN KEY (prodotto) REFERENCES public.prodotti(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: contenutoReparto contenutoreparto_reparti_supermercato_nome_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."contenutoReparto"
    ADD CONSTRAINT contenutoreparto_reparti_supermercato_nome_fk FOREIGN KEY ("supermercatoReparto", "nomeReparto") REFERENCES public.reparti(supermercato, nome) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: impiegati impiegati_persone_codf_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.impiegati
    ADD CONSTRAINT impiegati_persone_codf_fk FOREIGN KEY (persona) REFERENCES public.persone("codF") ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: impiegati impiegati_reparti_supermercato_nome_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.impiegati
    ADD CONSTRAINT impiegati_reparti_supermercato_nome_fk FOREIGN KEY (supermercato, "nomeReparto") REFERENCES public.reparti(supermercato, nome) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ingredienti ingredienti_prodotti_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ingredienti
    ADD CONSTRAINT ingredienti_prodotti_id_fk FOREIGN KEY (ingrediente) REFERENCES public.prodotti(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ingredienti ingredienti_prodotti_id_fk_2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ingredienti
    ADD CONSTRAINT ingredienti_prodotti_id_fk_2 FOREIGN KEY ("prodottoFinale") REFERENCES public.prodotti(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fornitori metodoPagamentoFornitore; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fornitori
    ADD CONSTRAINT "metodoPagamentoFornitore" FOREIGN KEY (pagamento) REFERENCES public."metodoPagamento"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: orari orari_supermercato_indirizzo_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orari
    ADD CONSTRAINT orari_supermercato_indirizzo_fk FOREIGN KEY (supermercato) REFERENCES public.supermercati(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: orariStraordinari oraristraordinari_supermercato_indirizzo_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."orariStraordinari"
    ADD CONSTRAINT oraristraordinari_supermercato_indirizzo_fk FOREIGN KEY (supermercato) REFERENCES public.supermercati(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ordiniPremi ordinepremi_clienti_ntessera_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."ordiniPremi"
    ADD CONSTRAINT ordinepremi_clienti_ntessera_fk FOREIGN KEY (cliente) REFERENCES public.clienti("nTessera") ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ordiniPremi ordinepremi_premi_codice_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."ordiniPremi"
    ADD CONSTRAINT ordinepremi_premi_codice_fk FOREIGN KEY (premio) REFERENCES public.premi(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ordini ordini_fornitore_iva_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ordini
    ADD CONSTRAINT ordini_fornitore_iva_fk FOREIGN KEY (fornitore) REFERENCES public.fornitori(iva) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ordini ordini_prodotti_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ordini
    ADD CONSTRAINT ordini_prodotti_id_fk FOREIGN KEY (prodotto) REFERENCES public.prodotti(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ordini ordini_supermercato_indirizzo_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ordini
    ADD CONSTRAINT ordini_supermercato_indirizzo_fk FOREIGN KEY (supermercato) REFERENCES public.supermercati(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: premiSpeciali premispeciali_premi_codice_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."premiSpeciali"
    ADD CONSTRAINT premispeciali_premi_codice_fk FOREIGN KEY (premio) REFERENCES public.premi(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: prenotazioni prenotazioni_clienti_ntessera_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prenotazioni
    ADD CONSTRAINT prenotazioni_clienti_ntessera_fk FOREIGN KEY (cliente) REFERENCES public.clienti("nTessera") ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: prenotazioni prenotazioni_premispeciali_premio_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prenotazioni
    ADD CONSTRAINT prenotazioni_premispeciali_premio_fk FOREIGN KEY ("premioSpeciale") REFERENCES public."premiSpeciali"(premio) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: prenotazioni prenotazioni_supermercati_codice_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prenotazioni
    ADD CONSTRAINT prenotazioni_supermercati_codice_fk FOREIGN KEY (supemercato) REFERENCES public.supermercati(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: punti punti_prodotti_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.punti
    ADD CONSTRAINT punti_prodotti_id_fk FOREIGN KEY (prodotto) REFERENCES public.prodotti(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: reparti reparti_supermercato_indirizzo_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reparti
    ADD CONSTRAINT reparti_supermercato_indirizzo_fk FOREIGN KEY (supermercato) REFERENCES public.supermercati(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: scontrini scontrini_casse_supermercato_ncassa_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.scontrini
    ADD CONSTRAINT scontrini_casse_supermercato_ncassa_fk FOREIGN KEY ("supermercatoCassa", "nCassa") REFERENCES public.casse(supermercato, "nCassa") ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: scontrini scontrini_clienti_ntessera_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.scontrini
    ADD CONSTRAINT scontrini_clienti_ntessera_fk FOREIGN KEY (cliente) REFERENCES public.clienti("nTessera") ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: scontrini scontrini_metodopagamento_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.scontrini
    ADD CONSTRAINT scontrini_metodopagamento_id_fk FOREIGN KEY (pagamento) REFERENCES public."metodoPagamento"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: scontrini scontrini_prenotazioni_codice_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.scontrini
    ADD CONSTRAINT scontrini_prenotazioni_codice_fk FOREIGN KEY (prenotazione) REFERENCES public.prenotazioni(codice);


--
-- Name: scortePremi scortepremi_premi_codice_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."scortePremi"
    ADD CONSTRAINT scortepremi_premi_codice_fk FOREIGN KEY (premio) REFERENCES public.premi(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: scortePremi scortepremi_supermercati_codice_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."scortePremi"
    ADD CONSTRAINT scortepremi_supermercati_codice_fk FOREIGN KEY (supermercato) REFERENCES public.supermercati(codice) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: turni turni_impiegati_matricola_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.turni
    ADD CONSTRAINT turni_impiegati_matricola_fk FOREIGN KEY (impiegato) REFERENCES public.impiegati(matricola) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: vendite vendite_prodotti_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendite
    ADD CONSTRAINT vendite_prodotti_id_fk FOREIGN KEY (prodotto) REFERENCES public.prodotti(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: vendite vendite_scontrini_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vendite
    ADD CONSTRAINT vendite_scontrini_id_fk FOREIGN KEY (scontrino) REFERENCES public.scontrini(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--



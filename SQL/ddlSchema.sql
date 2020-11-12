create table public.premi
(
	codice bigint not null
		constraint premi_pk
			primary key,
	nome varchar not null,
	categoria varchar not null,
	"puntiNecessari" integer not null
		constraint puntinecessariperunpremiomaggioridi0
			check ("puntiNecessari" > 0),
	"dataInizioVadilita" date not null,
	"dataFineValidita" date not null,
	constraint validitàpremioinunintervalloesistente
		check ("dataInizioVadilita" < "dataFineValidita")
);

alter table public.premi owner to postgres;

create table public.persone
(
	"codF" varchar(16) not null
		constraint persone_pk
			primary key,
	nome varchar(20) not null,
	cognome varchar(20) not null,
	indirizzo varchar not null,
	telefono varchar(12) not null,
	"dataNascita" date not null,
	mail varchar not null
);

alter table public.persone owner to postgres;

create unique index persone_mail_uindex
	on public.persone (mail);

create table public.supermercati
(
	codice varchar not null
		constraint supermercato_pk
			primary key,
	metriquadri double precision not null,
	indirizzo varchar
);

alter table public.supermercati owner to postgres;

create table public.prodotti
(
	id bigint not null
		constraint prodotti_pk
			primary key,
	nome varchar not null,
	prezzo double precision not null,
	categoria varchar not null,
	assemblato boolean default false not null,
	soglia integer not null
		constraint soglianonnegativa
			check (soglia >= 1)
);

alter table public.prodotti owner to postgres;

create table public."metodoPagamento"
(
	id serial not null
		constraint metodopagamento_pk
			primary key,
	tipo varchar not null
);

alter table public."metodoPagamento" owner to postgres;

create table public.fornitori
(
	iva varchar(11) not null
		constraint fornitore_pk
			primary key,
	"ragioneSociale" varchar not null,
	indirizzo varchar not null,
	telefono varchar(12) not null,
	mail varchar not null,
	pagamento integer not null
		constraint "metodoPagamentoFornitore"
			references public."metodoPagamento"
				on update cascade on delete cascade
);

alter table public.fornitori owner to postgres;

create table public.clienti
(
	"nTessera" varchar not null
		constraint clienti_pk
			primary key,
	"saldoPunti" integer not null
		constraint puntipositivi
			check ("saldoPunti" >= 0),
	persona varchar(16) not null
		constraint clienti_persone_codf_fk
			references public.persone
				on update cascade on delete cascade
);

alter table public.clienti owner to postgres;

create unique index clienti_persona_uindex
	on public.clienti (persona);

create table public.orari
(
	giorni varchar not null,
	supermercato varchar not null
		constraint orari_supermercato_indirizzo_fk
			references public.supermercati
				on update cascade on delete cascade,
	"oraApertura" time not null,
	"oraChiusura" time not null,
	constraint orari_pk
		primary key (giorni, supermercato),
	constraint primaaprepoichiude
		check ("oraChiusura" > "oraApertura")
);

alter table public.orari owner to postgres;

create table public."orariStraordinari"
(
	supermercato varchar not null
		constraint oraristraordinari_supermercato_indirizzo_fk
			references public.supermercati
				on update cascade on delete cascade,
	data varchar not null,
	"oraApertura" time not null,
	"oraChiusura" time not null,
	constraint oraristraordinari_pk
		primary key (supermercato, data),
	constraint primaaprepoichiudestraordinario
		check ("oraChiusura" > "oraApertura")
);

alter table public."orariStraordinari" owner to postgres;

create table public.ingredienti
(
	ingrediente integer not null
		constraint ingredienti_prodotti_id_fk
			references public.prodotti
				on update cascade on delete cascade,
	"prodottoFinale" integer not null
		constraint ingredienti_prodotti_id_fk_2
			references public.prodotti
				on update cascade on delete cascade,
	qta integer not null
		constraint stimatempogiornipositiva
			check (qta > 0),
	constraint table_name_pk
		primary key (ingrediente, "prodottoFinale")
);

alter table public.ingredienti owner to postgres;

create table public.punti
(
	prodotto integer not null
		constraint punti_prodotti_id_fk
			references public.prodotti
				on update cascade on delete cascade,
	"dataInserimento" date not null,
	"dataScadenza" date not null,
	"nPunti" integer not null
		constraint npuntichedaunprodottopositivi
			check ("nPunti" >= 1),
	constraint punti_pk
		primary key (prodotto, "dataInserimento"),
	constraint datainserimentoprodottopuntiprimadatascadenzapunti
		check ("dataInserimento" <= "dataScadenza")
);

alter table public.punti owner to postgres;

create table public."ordiniPremi"
(
	premio integer not null
		constraint ordinepremi_premi_codice_fk
			references public.premi
				on update cascade on delete cascade,
	cliente varchar not null
		constraint ordinepremi_clienti_ntessera_fk
			references public.clienti
				on update cascade on delete cascade,
	"dataOrdine" date not null,
	qta integer not null
		constraint qtaordinepremiopositiva
			check (qta >= 1),
	constraint ordinepremi_pk
		primary key (premio, cliente, "dataOrdine")
);

alter table public."ordiniPremi" owner to postgres;

create table public.reparti
(
	supermercato varchar not null
		constraint reparti_supermercato_indirizzo_fk
			references public.supermercati
				on update cascade on delete cascade,
	nome varchar not null,
	constraint reparti_pk
		primary key (supermercato, nome)
);

alter table public.reparti owner to postgres;

create table public.impiegati
(
	matricola serial not null
		constraint impiegati_pk
			primary key,
	"dataAssunzione" date not null
		constraint dataassunzionenonfutura
			check ("dataAssunzione" <= CURRENT_DATE),
	mansione varchar not null,
	livello integer not null
		constraint livelloimpiegato
			check ((livello >= 1) AND (livello <= 7)),
	supermercato varchar,
	"nomeReparto" varchar,
	persona varchar(16) not null
		constraint impiegati_persone_codf_fk
			references public.persone
				on update cascade on delete cascade,
	constraint impiegati_reparti_supermercato_nome_fk
		foreign key (supermercato, "nomeReparto") references public.reparti
			on update cascade on delete cascade
);

alter table public.impiegati owner to postgres;

create unique index impiegati_persona_uindex
	on public.impiegati (persona);

create table public.casse
(
	supermercato varchar not null
		constraint casse_supermercato_indirizzo_fk
			references public.supermercati
				on update cascade on delete cascade,
	"nCassa" integer not null,
	constraint casse_pk
		primary key (supermercato, "nCassa")
);

alter table public.casse owner to postgres;

create table public."contenutoReparto"
(
	"idBatch" integer not null
		constraint contenutoreparto_pk
			primary key,
	"supermercatoReparto" varchar not null,
	"nomeReparto" varchar not null,
	prodotto integer not null
		constraint contenutoreparto_prodotti_id_fk
			references public.prodotti
				on update cascade on delete cascade,
	"dataScadenza" date,
	qta integer not null
		constraint qtacontenutoinunreparto
			check (qta >= 0),
	constraint contenutoreparto_reparti_supermercato_nome_fk
		foreign key ("supermercatoReparto", "nomeReparto") references public.reparti
			on update cascade on delete cascade
);

alter table public."contenutoReparto" owner to postgres;

create unique index contenutoreparto_prodotto_datascadenza_nomereparto_supermercato
	on public."contenutoReparto" (prodotto, "dataScadenza", "nomeReparto", "supermercatoReparto");

create table public.ordini
(
	"codiceInterno" integer not null,
	"dataOrdine" date not null
		constraint dataordinenonfutura
			check ("dataOrdine" <= CURRENT_DATE),
	qta integer not null
		constraint qtaordinestrettamentepositiva
			check (qta > 0),
	fornitore varchar(16) not null
		constraint ordini_fornitore_iva_fk
			references public.fornitori
				on update cascade on delete cascade,
	prodotto integer not null
		constraint ordini_prodotti_id_fk
			references public.prodotti
				on update cascade on delete cascade,
	supermercato varchar not null
		constraint ordini_supermercato_indirizzo_fk
			references public.supermercati
				on update cascade on delete cascade,
	"dataScadenza" date
		constraint datascadenzafutura
			check ("dataScadenza" >= CURRENT_DATE),
	"giorniConsegnaStimata" integer not null
		constraint stimatempogiornipositiva
			check ("giorniConsegnaStimata" >= 0),
	constraint ordini_pk
		primary key ("dataOrdine", fornitore, prodotto, supermercato)
);

alter table public.ordini owner to postgres;

create table public.turni
(
	impiegato integer not null
		constraint turni_impiegati_matricola_fk
			references public.impiegati
				on update cascade on delete cascade,
	giorno varchar not null
		constraint eungiornodellasettimana
			check (((giorno)::text = 'Lunedi'::text) OR ((giorno)::text = 'Martedi'::text) OR ((giorno)::text = 'Mercoledi'::text) OR ((giorno)::text = 'Giovedi'::text) OR ((giorno)::text = 'Venerdi'::text) OR ((giorno)::text = 'Sabato'::text) OR ((giorno)::text = 'Domenica'::text)),
	"inizioOra" time not null,
	"fineOra" time not null,
	constraint turni_pk
		primary key (impiegato, giorno)
);

alter table public.turni owner to postgres;

create table public.cassieri
(
	impiegato integer not null
		constraint cassieri_impiegati_matricola_fk
			references public.impiegati
				on update cascade on delete cascade,
	"supermercatoCassa" varchar not null,
	"nCassa" integer not null,
	turno varchar not null,
	constraint cassieri_pk
		primary key (impiegato, "supermercatoCassa", "nCassa"),
	constraint cassieri_casse_supermercato_ncassa_fk
		foreign key ("supermercatoCassa", "nCassa") references public.casse
			on update cascade on delete cascade
);

alter table public.cassieri owner to postgres;

create table public."premiSpeciali"
(
	premio bigint not null
		constraint premispeciali_pk
			primary key
		constraint premispeciali_premi_codice_fk
			references public.premi
				on update cascade on delete cascade,
	"partePunti" integer not null
		constraint puntiscontatipositivi
			check ("partePunti" > 0),
	"parteDenaro" double precision not null
		constraint denaropositivo
			check ("parteDenaro" > (0)::double precision)
);

alter table public."premiSpeciali" owner to postgres;

create table public.prenotazioni
(
	"premioSpeciale" bigint not null
		constraint prenotazioni_premispeciali_premio_fk
			references public."premiSpeciali"
				on update cascade on delete cascade,
	supemercato varchar not null
		constraint prenotazioni_supermercati_codice_fk
			references public.supermercati
				on update cascade on delete cascade,
	cliente varchar not null
		constraint prenotazioni_clienti_ntessera_fk
			references public.clienti
				on update cascade on delete cascade,
	"timestampOrdine" timestamp not null,
	modalita varchar not null
		constraint modalitàpuntiomista
			check (((modalita)::text = 'punti'::text) OR ((modalita)::text = 'mista'::text)),
	qta integer not null
		constraint qtapositiva
			check (qta > 0),
	codice varchar not null
		constraint prenotazioni_pk
			primary key,
	disponibile boolean default false not null
);

alter table public.prenotazioni owner to postgres;

create table public.scontrini
(
	id integer not null
		constraint scontrini_pk
			primary key,
	"dataEmissione" date not null
		constraint scontrininonnelfuturo
			check ("dataEmissione" <= CURRENT_DATE),
	pagamento integer not null
		constraint scontrini_metodopagamento_id_fk
			references public."metodoPagamento"
				on update cascade on delete cascade,
	"nCassa" integer not null,
	"supermercatoCassa" varchar not null,
	cliente varchar
		constraint scontrini_clienti_ntessera_fk
			references public.clienti
				on update cascade on delete cascade,
	prenotazione varchar
		constraint scontrini_prenotazioni_codice_fk
			references public.prenotazioni,
	constraint scontrini_casse_supermercato_ncassa_fk
		foreign key ("supermercatoCassa", "nCassa") references public.casse
			on update cascade on delete cascade
);

alter table public.scontrini owner to postgres;

create table public.vendite
(
	scontrino integer not null
		constraint vendite_scontrini_id_fk
			references public.scontrini
				on update cascade on delete cascade,
	prodotto integer not null
		constraint vendite_prodotti_id_fk
			references public.prodotti
				on update cascade on delete cascade,
	qta integer not null
		constraint vendutoalmenounprodtto
			check (qta > 0),
	"prezzoUnità" double precision not null
		constraint prezzovenditaminimo0
			check ("prezzoUnità" >= (0)::double precision),
	constraint vendite_pk
		primary key (scontrino, prodotto)
);

alter table public.vendite owner to postgres;

create unique index prenotazioni_premiospeciale_cliente_timestampordine_uindex
	on public.prenotazioni ("premioSpeciale", cliente, "timestampOrdine");

create table public."scortePremi"
(
	supermercato varchar not null
		constraint scortepremi_supermercati_codice_fk
			references public.supermercati
				on update cascade on delete cascade,
	premio bigint not null
		constraint scortepremi_premi_codice_fk
			references public.premi
				on update cascade on delete cascade,
	qta integer not null
		constraint qtapositivaougualeazero
			check (qta >= 0),
	constraint scortepremi_pk
		primary key (supermercato, premio)
);

alter table public."scortePremi" owner to postgres;



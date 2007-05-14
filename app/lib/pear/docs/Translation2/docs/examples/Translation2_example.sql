CREATE TABLE mytable_i18n (
  id TEXT NOT NULL,
  page_id varchar(100),
  en text,
  de text,
  es text,
  fr text,
  it text
);

CREATE UNIQUE INDEX mytable_i18n_id_index ON mytable_i18n (id(16), page_id);

INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("month_01", "calendar", "january", NULL, NULL, NULL, "gennaio");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("month_02", "calendar", "february", NULL, NULL, NULL, "febbraio");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("month_03", "calendar", "march", NULL, NULL, NULL, "marzo");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("month_04", "calendar", "april", NULL, NULL, NULL, "aprile");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("month_05", "calendar", "may", NULL, NULL, NULL, "maggio");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("month_06", "calendar", "june", NULL, NULL, NULL, "giugno");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("month_07", "calendar", "july", NULL, NULL, NULL, "luglio");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("month_08", "calendar", "august", NULL, NULL, NULL, "agosto");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("month_09", "calendar", "september", NULL, NULL, NULL, "settembre");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("month_10", "calendar", "october", NULL, NULL, NULL, "ottobre");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("month_11", "calendar", "november", NULL, NULL, NULL, "novembre");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("month_12", "calendar", "december", NULL, NULL, NULL, "dicembre");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("day_0", "calendar", "sunday", NULL, NULL, NULL, "domenica");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("day_1", "calendar", "monday", NULL, NULL, NULL, "lunedì");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("day_2", "calendar", "tuesday", NULL, NULL, NULL, "martedì");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("day_3", "calendar", "wednesday", NULL, NULL, NULL, "mercoledì");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("day_4", "calendar", "thursday", NULL, NULL, NULL, "giovedì");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("day_5", "calendar", "friday", NULL, NULL, NULL, "venerdì");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("day_6", "calendar", "saturday", NULL, NULL, NULL, "sabato");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("only_english", NULL, "only english text", NULL, NULL, NULL, NULL);
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("only_italian", NULL, NULL, NULL, NULL, NULL, "testo solo in italiano");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("hello_user", NULL, "hello &&user&&, today is &&weekday&&, &&day&&th &&month&& &&year&&", NULL, NULL, NULL, "ciao, &&user&&, oggi è il &&day&& &&month&& &&year&& (&&weekday&&)");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("alone", "alone", "all alone", NULL, NULL, NULL, "solo soletto");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("isempty", NULL, NULL, "this string is empty in English and Italian, but not in German!", NULL, NULL, NULL);
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("prova_conflitto", "in_page", "conflicting text - in page", NULL, NULL, NULL, "testo con conflitto - in page");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("prova_conflitto", NULL, "conflicting text - Global", NULL, NULL, NULL, "testo con conflitto - globale");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("page_id_vuoto", "", "string with empty page_id (i.e. NOT NULL)", NULL, NULL, NULL, "stringa con page_id vuoto ma non nullo");
INSERT INTO mytable_i18n (id, page_id, en, de, es, fr, it) VALUES("test", NULL, "this is a test string", NULL, NULL, NULL, "stringa di prova");


CREATE TABLE mytable_langs_avail (
  id varchar(10) NOT NULL,
  name varchar(200),
  meta text,
  error_text varchar(250),
  encoding varchar(16) NOT NULL DEFAULT 'iso-8859-1'
);

CREATE UNIQUE INDEX mytable_langs_avail_id_index ON mytable_langs_avail (id);


INSERT INTO mytable_langs_avail (id, name, meta, error_text, encoding) VALUES("en", "english", "my meta info", "not available", "iso-8859-1");
INSERT INTO mytable_langs_avail (id, name, meta, error_text, encoding) VALUES("de", "deutsch", "iso-8859-1", "kein Text auf Deutsch verfügbar", "iso-8859-1");
INSERT INTO mytable_langs_avail (id, name, meta, error_text, encoding) VALUES("it", "italiano", "charset=iso-8859-1", "non disponibile", "iso-8859-1");

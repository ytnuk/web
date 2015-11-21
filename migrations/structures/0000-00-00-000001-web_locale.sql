--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: web_locale; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE web_locale (
    id integer NOT NULL,
    web_id character varying NOT NULL,
    locale_id character varying NOT NULL,
    "primary" boolean
);


--
-- Name: web_locale_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE web_locale_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: web_locale_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE web_locale_id_seq OWNED BY web_locale.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY web_locale ALTER COLUMN id SET DEFAULT nextval('web_locale_id_seq'::regclass);


--
-- Name: web_locale_id; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY web_locale
    ADD CONSTRAINT web_locale_id PRIMARY KEY (id);


--
-- Name: web_locale_web_id_locale_id; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY web_locale
    ADD CONSTRAINT web_locale_web_id_locale_id UNIQUE (web_id, locale_id);


--
-- Name: web_locale_web_id_primary; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY web_locale
    ADD CONSTRAINT web_locale_web_id_primary UNIQUE (web_id, "primary") DEFERRABLE INITIALLY DEFERRED;


--
-- Name: web_locale_locale_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX web_locale_locale_id ON web_locale USING btree (locale_id);


--
-- Name: web_locale_web_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX web_locale_web_id ON web_locale USING btree (web_id);


--
-- Name: web_locale_locale_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY web_locale
    ADD CONSTRAINT web_locale_locale_id_fkey FOREIGN KEY (locale_id) REFERENCES translation_locale(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: web_locale_web_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY web_locale
    ADD CONSTRAINT web_locale_web_id_fkey FOREIGN KEY (web_id) REFERENCES web(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--


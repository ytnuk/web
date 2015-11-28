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

ALTER TABLE IF EXISTS ONLY public.web_domain_locale DROP CONSTRAINT IF EXISTS web_domain_locale_locale_id_fkey;
ALTER TABLE IF EXISTS ONLY public.web_domain_locale DROP CONSTRAINT IF EXISTS web_domain_locale_domain_id_fkey;
ALTER TABLE IF EXISTS ONLY public.web_domain_locale DROP CONSTRAINT IF EXISTS web_domain_locale_pkey;
DROP TABLE IF EXISTS public.web_domain_locale;
SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: web_domain_locale; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE web_domain_locale (
    id integer DEFAULT nextval('web_domain_locale_id_seq'::regclass) NOT NULL,
    domain_id character varying NOT NULL,
    locale_id character varying NOT NULL,
    "primary" boolean
);


--
-- Name: web_domain_locale_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY web_domain_locale
    ADD CONSTRAINT web_domain_locale_pkey PRIMARY KEY (id);


--
-- Name: web_domain_locale_domain_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY web_domain_locale
    ADD CONSTRAINT web_domain_locale_domain_id_fkey FOREIGN KEY (domain_id) REFERENCES web_domain(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: web_domain_locale_locale_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY web_domain_locale
    ADD CONSTRAINT web_domain_locale_locale_id_fkey FOREIGN KEY (locale_id) REFERENCES translation_locale(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--


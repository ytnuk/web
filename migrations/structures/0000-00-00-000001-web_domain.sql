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

ALTER TABLE IF EXISTS ONLY public.web_domain DROP CONSTRAINT IF EXISTS web_domain_web_id_fkey;
ALTER TABLE IF EXISTS ONLY public.web_domain DROP CONSTRAINT IF EXISTS web_domain_pkey;
DROP TABLE IF EXISTS public.web_domain;
SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: web_domain; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE web_domain (
    id character varying NOT NULL,
    web_id character varying NOT NULL,
    secured boolean DEFAULT true NOT NULL
);


--
-- Name: web_domain_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY web_domain
    ADD CONSTRAINT web_domain_pkey PRIMARY KEY (id);


--
-- Name: web_domain_web_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY web_domain
    ADD CONSTRAINT web_domain_web_id_fkey FOREIGN KEY (web_id) REFERENCES web(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--


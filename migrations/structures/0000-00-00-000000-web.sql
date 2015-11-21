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
-- Name: web; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE web (
    id character varying NOT NULL,
    menu_id integer NOT NULL,
    name integer NOT NULL
);


--
-- Name: web_id; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY web
    ADD CONSTRAINT web_id PRIMARY KEY (id);


--
-- Name: web_menu_id; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY web
    ADD CONSTRAINT web_menu_id UNIQUE (menu_id);


--
-- Name: web_name; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY web
    ADD CONSTRAINT web_name UNIQUE (name);


--
-- Name: web_menu_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY web
    ADD CONSTRAINT web_menu_id_fkey FOREIGN KEY (menu_id) REFERENCES menu(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: web_name_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY web
    ADD CONSTRAINT web_name_fkey FOREIGN KEY (name) REFERENCES translation(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--


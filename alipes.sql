--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: comps; Type: TABLE; Schema: public; Owner: apache; Tablespace: 
--

CREATE TABLE comps (
    name character varying,
    ip character varying,
    port integer,
    switch integer,
    id integer,
    bandwidth character varying
);


ALTER TABLE public.comps OWNER TO apache;

--
-- Name: seq; Type: SEQUENCE; Schema: public; Owner: apache
--

CREATE SEQUENCE seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.seq OWNER TO apache;

--
-- Name: seq; Type: SEQUENCE SET; Schema: public; Owner: apache
--

SELECT pg_catalog.setval('seq', 15, true);


--
-- Data for Name: comps; Type: TABLE DATA; Schema: public; Owner: apache
--

COPY comps (name, ip, port, switch, id, bandwidth) FROM stdin;
enet	anotherrandomip	3	1	14	high
new2	randomip	2	1	13	high
thing	otherthing	1	2	15	high
alex	192.168.1.58	1	1	9	high
\.


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--


--
-- PostgreSQL database dump
--

\restrict trFfOqPKe6urcVZzAuqjDlEJUR1GwdtLfneVGLL5amAO7UdLTlsnnUYNxtYPtjt

-- Dumped from database version 16.10
-- Dumped by pg_dump version 16.10

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
-- Name: marketplace; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA marketplace;


ALTER SCHEMA marketplace OWNER TO postgres;

--
-- Name: generate_order_number(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.generate_order_number() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.order_number = 'ORD-' || TO_CHAR(CURRENT_TIMESTAMP, 'YYYYMMDD') || '-' || LPAD(NEW.id::TEXT, 6, '0');
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.generate_order_number() OWNER TO postgres;

--
-- Name: update_product_rating(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_product_rating() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE products SET
        rating_average = (
            SELECT COALESCE(AVG(rating), 0)
            FROM reviews
            WHERE product_id = NEW.product_id AND is_approved = TRUE
        ),
        rating_count = (
            SELECT COUNT(*)
            FROM reviews
            WHERE product_id = NEW.product_id AND is_approved = TRUE
        )
    WHERE id = NEW.product_id;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_product_rating() OWNER TO postgres;

--
-- Name: update_updated_at_column(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_updated_at_column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_updated_at_column() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: activity_logs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.activity_logs (
    id integer NOT NULL,
    user_id integer,
    action character varying(100) NOT NULL,
    entity_type character varying(50),
    entity_id integer,
    ip_address inet,
    user_agent text,
    metadata jsonb,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.activity_logs OWNER TO postgres;

--
-- Name: activity_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.activity_logs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.activity_logs_id_seq OWNER TO postgres;

--
-- Name: activity_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.activity_logs_id_seq OWNED BY public.activity_logs.id;


--
-- Name: categories; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categories (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    slug character varying(100) NOT NULL,
    description text,
    icon character varying(50),
    parent_id integer,
    is_active boolean DEFAULT true,
    display_order integer DEFAULT 0,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.categories OWNER TO postgres;

--
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.categories_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categories_id_seq OWNER TO postgres;

--
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- Name: order_items; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.order_items (
    id integer NOT NULL,
    order_id integer NOT NULL,
    product_id integer NOT NULL,
    seller_id integer NOT NULL,
    product_title character varying(255) NOT NULL,
    product_price numeric(10,2) NOT NULL,
    quantity integer DEFAULT 1,
    seller_amount numeric(10,2) NOT NULL,
    platform_fee numeric(10,2) NOT NULL,
    license_key character varying(100),
    download_count integer DEFAULT 0,
    max_downloads integer DEFAULT 3,
    review_id integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.order_items OWNER TO postgres;

--
-- Name: order_items_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.order_items_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.order_items_id_seq OWNER TO postgres;

--
-- Name: order_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.order_items_id_seq OWNED BY public.order_items.id;


--
-- Name: orders; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.orders (
    id integer NOT NULL,
    order_number character varying(50) NOT NULL,
    buyer_id integer NOT NULL,
    subtotal numeric(10,2) NOT NULL,
    discount numeric(10,2) DEFAULT 0.00,
    total_amount numeric(10,2) NOT NULL,
    platform_fee numeric(10,2) DEFAULT 0.00,
    payment_method character varying(50) DEFAULT 'stripe'::character varying,
    payment_status character varying(20) DEFAULT 'pending'::character varying,
    stripe_payment_id character varying(255),
    stripe_session_id character varying(255),
    promo_code_id integer,
    promo_discount numeric(10,2) DEFAULT 0.00,
    status character varying(20) DEFAULT 'pending'::character varying,
    paid_at timestamp without time zone,
    completed_at timestamp without time zone,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT check_payment_status CHECK (((payment_status)::text = ANY ((ARRAY['pending'::character varying, 'processing'::character varying, 'completed'::character varying, 'failed'::character varying, 'refunded'::character varying])::text[]))),
    CONSTRAINT check_status CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'processing'::character varying, 'completed'::character varying, 'cancelled'::character varying, 'refunded'::character varying])::text[])))
);


ALTER TABLE public.orders OWNER TO postgres;

--
-- Name: orders_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.orders_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.orders_id_seq OWNER TO postgres;

--
-- Name: orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.orders_id_seq OWNED BY public.orders.id;


--
-- Name: orders_with_details; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.orders_with_details AS
SELECT
    NULL::integer AS id,
    NULL::character varying(50) AS order_number,
    NULL::integer AS buyer_id,
    NULL::numeric(10,2) AS subtotal,
    NULL::numeric(10,2) AS discount,
    NULL::numeric(10,2) AS total_amount,
    NULL::numeric(10,2) AS platform_fee,
    NULL::character varying(50) AS payment_method,
    NULL::character varying(20) AS payment_status,
    NULL::character varying(255) AS stripe_payment_id,
    NULL::character varying(255) AS stripe_session_id,
    NULL::integer AS promo_code_id,
    NULL::numeric(10,2) AS promo_discount,
    NULL::character varying(20) AS status,
    NULL::timestamp without time zone AS paid_at,
    NULL::timestamp without time zone AS completed_at,
    NULL::timestamp without time zone AS created_at,
    NULL::timestamp without time zone AS updated_at,
    NULL::character varying(255) AS buyer_name,
    NULL::character varying(255) AS buyer_email,
    NULL::bigint AS items_count;


ALTER VIEW public.orders_with_details OWNER TO postgres;

--
-- Name: product_gallery; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product_gallery (
    id integer NOT NULL,
    product_id integer NOT NULL,
    image_url character varying(500) NOT NULL,
    display_order integer DEFAULT 0,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.product_gallery OWNER TO postgres;

--
-- Name: product_gallery_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.product_gallery_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_gallery_id_seq OWNER TO postgres;

--
-- Name: product_gallery_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_gallery_id_seq OWNED BY public.product_gallery.id;


--
-- Name: product_tags; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product_tags (
    id integer NOT NULL,
    product_id integer NOT NULL,
    tag_id integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.product_tags OWNER TO postgres;

--
-- Name: product_tags_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.product_tags_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_tags_id_seq OWNER TO postgres;

--
-- Name: product_tags_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_tags_id_seq OWNED BY public.product_tags.id;


--
-- Name: products; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.products (
    id integer NOT NULL,
    seller_id integer NOT NULL,
    category_id integer,
    title character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    description text NOT NULL,
    short_description text,
    price numeric(10,2) NOT NULL,
    original_price numeric(10,2),
    file_url character varying(500) NOT NULL,
    file_size bigint,
    file_type character varying(50),
    thumbnail_url character varying(500),
    preview_url character varying(500),
    demo_url character varying(500),
    license_type character varying(50) DEFAULT 'single'::character varying,
    downloads integer DEFAULT 0,
    views integer DEFAULT 0,
    sales integer DEFAULT 0,
    revenue numeric(12,2) DEFAULT 0.00,
    rating_average numeric(3,2) DEFAULT 0.00,
    rating_count integer DEFAULT 0,
    status character varying(20) DEFAULT 'pending'::character varying,
    is_featured boolean DEFAULT false,
    rejection_reason text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    approved_at timestamp without time zone,
    CONSTRAINT check_status CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'approved'::character varying, 'rejected'::character varying, 'suspended'::character varying])::text[])))
);


ALTER TABLE public.products OWNER TO postgres;

--
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.products_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.products_id_seq OWNER TO postgres;

--
-- Name: products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.products_id_seq OWNED BY public.products.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer NOT NULL,
    full_name character varying(255) NOT NULL,
    username character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    role character varying(20) DEFAULT 'buyer'::character varying NOT NULL,
    avatar_url character varying(500),
    bio text,
    is_active boolean DEFAULT true,
    email_verified boolean DEFAULT false,
    remember_token character varying(100),
    last_login timestamp without time zone,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    shop_name character varying(255),
    shop_slug character varying(255),
    shop_description text,
    shop_logo character varying(500),
    shop_banner character varying(500),
    total_sales numeric(12,2) DEFAULT 0.00,
    total_earnings numeric(12,2) DEFAULT 0.00,
    total_products integer DEFAULT 0,
    rating_average numeric(3,2) DEFAULT 0.00,
    rating_count integer DEFAULT 0,
    CONSTRAINT check_role CHECK (((role)::text = ANY ((ARRAY['buyer'::character varying, 'seller'::character varying, 'admin'::character varying])::text[])))
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: products_with_seller; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.products_with_seller AS
 SELECT p.id,
    p.seller_id,
    p.category_id,
    p.title,
    p.slug,
    p.description,
    p.short_description,
    p.price,
    p.original_price,
    p.file_url,
    p.file_size,
    p.file_type,
    p.thumbnail_url,
    p.preview_url,
    p.demo_url,
    p.license_type,
    p.downloads,
    p.views,
    p.sales,
    p.revenue,
    p.rating_average,
    p.rating_count,
    p.status,
    p.is_featured,
    p.rejection_reason,
    p.created_at,
    p.updated_at,
    p.approved_at,
    u.username AS seller_username,
    u.shop_name AS seller_shop_name,
    u.rating_average AS seller_rating,
    c.name AS category_name,
    c.slug AS category_slug
   FROM ((public.products p
     LEFT JOIN public.users u ON ((p.seller_id = u.id)))
     LEFT JOIN public.categories c ON ((p.category_id = c.id)));


ALTER VIEW public.products_with_seller OWNER TO postgres;

--
-- Name: promo_codes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.promo_codes (
    id integer NOT NULL,
    code character varying(50) NOT NULL,
    type character varying(20) DEFAULT 'percentage'::character varying NOT NULL,
    value numeric(10,2) NOT NULL,
    min_purchase numeric(10,2) DEFAULT 0.00,
    max_uses integer,
    used_count integer DEFAULT 0,
    expires_at timestamp without time zone,
    is_active boolean DEFAULT true,
    created_by integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT check_promo_type CHECK (((type)::text = ANY ((ARRAY['percentage'::character varying, 'fixed'::character varying])::text[])))
);


ALTER TABLE public.promo_codes OWNER TO postgres;

--
-- Name: promo_codes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.promo_codes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.promo_codes_id_seq OWNER TO postgres;

--
-- Name: promo_codes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.promo_codes_id_seq OWNED BY public.promo_codes.id;


--
-- Name: reviews; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.reviews (
    id integer NOT NULL,
    product_id integer NOT NULL,
    buyer_id integer NOT NULL,
    order_item_id integer,
    rating integer NOT NULL,
    title character varying(255),
    comment text,
    is_verified_purchase boolean DEFAULT false,
    is_approved boolean DEFAULT true,
    seller_response text,
    seller_responded_at timestamp without time zone,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT check_rating CHECK (((rating >= 1) AND (rating <= 5)))
);


ALTER TABLE public.reviews OWNER TO postgres;

--
-- Name: reviews_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.reviews_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.reviews_id_seq OWNER TO postgres;

--
-- Name: reviews_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.reviews_id_seq OWNED BY public.reviews.id;


--
-- Name: tags; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tags (
    id integer NOT NULL,
    name character varying(50) NOT NULL,
    slug character varying(50) NOT NULL,
    usage_count integer DEFAULT 0,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.tags OWNER TO postgres;

--
-- Name: tags_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tags_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tags_id_seq OWNER TO postgres;

--
-- Name: tags_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tags_id_seq OWNED BY public.tags.id;


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: wishlist; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.wishlist (
    id integer NOT NULL,
    user_id integer NOT NULL,
    product_id integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.wishlist OWNER TO postgres;

--
-- Name: wishlist_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.wishlist_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.wishlist_id_seq OWNER TO postgres;

--
-- Name: wishlist_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.wishlist_id_seq OWNED BY public.wishlist.id;


--
-- Name: activity_logs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.activity_logs ALTER COLUMN id SET DEFAULT nextval('public.activity_logs_id_seq'::regclass);


--
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- Name: order_items id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items ALTER COLUMN id SET DEFAULT nextval('public.order_items_id_seq'::regclass);


--
-- Name: orders id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders ALTER COLUMN id SET DEFAULT nextval('public.orders_id_seq'::regclass);


--
-- Name: product_gallery id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_gallery ALTER COLUMN id SET DEFAULT nextval('public.product_gallery_id_seq'::regclass);


--
-- Name: product_tags id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_tags ALTER COLUMN id SET DEFAULT nextval('public.product_tags_id_seq'::regclass);


--
-- Name: products id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products ALTER COLUMN id SET DEFAULT nextval('public.products_id_seq'::regclass);


--
-- Name: promo_codes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.promo_codes ALTER COLUMN id SET DEFAULT nextval('public.promo_codes_id_seq'::regclass);


--
-- Name: reviews id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reviews ALTER COLUMN id SET DEFAULT nextval('public.reviews_id_seq'::regclass);


--
-- Name: tags id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags ALTER COLUMN id SET DEFAULT nextval('public.tags_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: wishlist id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlist ALTER COLUMN id SET DEFAULT nextval('public.wishlist_id_seq'::regclass);


--
-- Data for Name: activity_logs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.activity_logs (id, user_id, action, entity_type, entity_id, ip_address, user_agent, metadata, created_at) FROM stdin;
\.


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.categories (id, name, slug, description, icon, parent_id, is_active, display_order, created_at, updated_at) FROM stdin;
1	Templates	templates	Templates web et design	🎨	\N	t	1	2026-01-12 04:39:30.791816	2026-01-12 04:39:30.791816
2	Graphics	graphics	Ressources graphiques	🖼️	\N	t	2	2026-01-12 04:39:30.791816	2026-01-12 04:39:30.791816
3	Code	code	Scripts et codes source	💻	\N	t	3	2026-01-12 04:39:30.791816	2026-01-12 04:39:30.791816
4	Courses	courses	Formations et tutoriels	📚	\N	t	4	2026-01-12 04:39:30.791816	2026-01-12 04:39:30.791816
5	Photos	photos	Photos stock	📸	\N	t	5	2026-01-12 04:39:30.791816	2026-01-12 04:39:30.791816
6	Audio	audio	Musiques et sons	🎵	\N	t	6	2026-01-12 04:39:30.791816	2026-01-12 04:39:30.791816
7	Fonts	fonts	Polices de caractères	🔤	\N	t	7	2026-01-12 04:39:30.791816	2026-01-12 04:39:30.791816
8	Other	other	Autres produits digitaux	📦	\N	t	8	2026-01-12 04:39:30.791816	2026-01-12 04:39:30.791816
9	UI Kits	ui-kits	Kits d'interface utilisateur prêts à l'emploi	\N	\N	t	0	2026-01-13 13:37:13.779704	2026-01-13 13:37:13.779704
11	Icônes	icones	Packs d'icônes et pictogrammes vectoriels	\N	\N	t	0	2026-01-13 13:37:13.779704	2026-01-13 13:37:13.779704
12	Illustrations	illustrations	Illustrations vectorielles et images premium	\N	\N	t	0	2026-01-13 13:37:13.779704	2026-01-13 13:37:13.779704
\.


--
-- Data for Name: order_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.order_items (id, order_id, product_id, seller_id, product_title, product_price, quantity, seller_amount, platform_fee, license_key, download_count, max_downloads, review_id, created_at) FROM stdin;
1	2	61	12	Portfolio Créatif Minimaliste	29.99	1	26.99	3.00	7AD6-474F-8B72-03A0	0	3	\N	2026-01-22 12:05:27.005734
2	3	66	19	Formation React Avancé	89.99	1	80.99	9.00	AD5F-1048-DE0F-5D5A	0	3	\N	2026-01-22 13:03:01.013949
3	4	66	19	Formation React Avancé	89.99	1	80.99	9.00	12F7-77D7-AAA4-7EF7	0	3	\N	2026-01-22 13:24:21.005182
4	5	66	19	Formation React Avancé	89.99	1	80.99	9.00	BC17-F4E7-5442-6D29	0	3	\N	2026-01-22 13:26:11.603572
5	6	66	19	Formation React Avancé	89.99	1	80.99	9.00	868D-6CFA-2137-41BD	0	3	\N	2026-01-22 13:27:25.421503
6	7	66	19	Formation React Avancé	89.99	1	80.99	9.00	66DD-E441-3756-49AF	0	3	\N	2026-01-22 13:28:34.408961
7	8	66	19	Formation React Avancé	89.99	1	80.99	9.00	875B-AA6C-2FBE-9243	0	3	\N	2026-01-22 13:28:46.208972
8	9	66	19	Formation React Avancé	89.99	1	80.99	9.00	0180-2CBF-2C3A-16E0	0	3	\N	2026-01-22 13:49:27.490217
9	10	66	19	Formation React Avancé	89.99	1	80.99	9.00	ADBC-AC1A-479F-B9AB	0	3	\N	2026-01-22 14:07:25.360275
10	11	66	19	Formation React Avancé	89.99	1	80.99	9.00	C834-BB04-5E45-A45D	0	3	\N	2026-01-22 14:08:07.223283
11	12	66	19	Formation React Avancé	89.99	1	80.99	9.00	21E7-540B-BB09-CC6D	0	3	\N	2026-01-22 14:13:40.113141
12	13	66	19	Formation React Avancé	89.99	1	80.99	9.00	ED56-BF08-6BDD-DBCD	0	3	\N	2026-01-22 14:40:59.274275
13	14	66	19	Formation React Avancé	89.99	1	80.99	9.00	872C-2001-E9EB-21EC	0	3	\N	2026-01-22 14:52:45.155733
14	15	61	12	Portfolio Créatif Minimaliste	29.99	1	26.99	3.00	4A31-F4CE-85DE-6256	0	3	\N	2026-01-23 08:36:54.319927
15	16	64	21	Nature et Paysage 4K	39.99	1	35.99	4.00	767C-0A50-F19F-D5C6	0	3	\N	2026-01-23 09:26:39.244645
16	17	64	21	Nature et Paysage 4K	39.99	1	35.99	4.00	137C-2380-467F-16A8	0	3	\N	2026-01-23 09:39:48.552154
17	18	64	21	Nature et Paysage 4K	39.99	1	35.99	4.00	AE35-5DE3-3856-FDA9	0	3	\N	2026-01-23 09:40:24.003306
18	19	66	19	Formation React Avancé	1.00	1	0.90	0.10	A53D-A702-BF04-001E	0	3	\N	2026-01-23 10:09:41.448301
19	20	66	19	Formation React Avancé	1.00	1	0.90	0.10	02D9-EE33-0D13-0CA2	0	3	\N	2026-01-23 10:10:09.651393
20	21	66	19	Formation React Avancé	1.00	1	0.90	0.10	1ADC-4388-50A9-7D16	0	3	\N	2026-01-23 10:10:22.430247
\.


--
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.orders (id, order_number, buyer_id, subtotal, discount, total_amount, platform_fee, payment_method, payment_status, stripe_payment_id, stripe_session_id, promo_code_id, promo_discount, status, paid_at, completed_at, created_at, updated_at) FROM stdin;
2	ORD-20260122-000002	20	29.99	0.00	29.99	0.00	stripe	pending	\N	\N	\N	0.00	pending	\N	\N	2026-01-22 12:05:27.005734	2026-01-22 12:05:27.005734
3	ORD-20260122-000003	20	89.99	9.00	80.99	0.00	stripe	pending	\N	\N	\N	9.00	pending	\N	\N	2026-01-22 13:03:01.013949	2026-01-22 13:03:01.013949
4	ORD-20260122-000004	20	89.99	9.00	80.99	0.00	stripe	pending	\N	\N	\N	9.00	pending	\N	\N	2026-01-22 13:24:21.005182	2026-01-22 13:24:21.005182
5	ORD-20260122-000005	20	89.99	9.00	80.99	0.00	stripe	pending	\N	\N	\N	9.00	pending	\N	\N	2026-01-22 13:26:11.603572	2026-01-22 13:26:11.603572
6	ORD-20260122-000006	20	89.99	9.00	80.99	0.00	stripe	pending	\N	\N	\N	9.00	pending	\N	\N	2026-01-22 13:27:25.421503	2026-01-22 13:27:25.421503
7	ORD-20260122-000007	20	89.99	9.00	80.99	0.00	stripe	pending	\N	\N	\N	9.00	pending	\N	\N	2026-01-22 13:28:34.408961	2026-01-22 13:28:34.408961
8	ORD-20260122-000008	20	89.99	9.00	80.99	0.00	stripe	pending	\N	\N	\N	9.00	pending	\N	\N	2026-01-22 13:28:46.208972	2026-01-22 13:28:46.208972
9	ORD-20260122-000009	20	89.99	9.00	80.99	0.00	stripe	pending	\N	\N	\N	9.00	pending	\N	\N	2026-01-22 13:49:27.490217	2026-01-22 13:49:27.490217
10	ORD-20260122-000010	20	89.99	9.00	80.99	0.00	stripe	pending	\N	cs_test_a1m0YXMSmsJpH7lGpa3aSxsQg2Zmu4GjYdEfIfPa6dzXPU4Mbb9wxcYIPx	\N	9.00	pending	\N	\N	2026-01-22 14:07:25.360275	2026-01-22 14:07:26.738598
11	ORD-20260122-000011	20	89.99	9.00	80.99	0.00	stripe	pending	\N	cs_test_a17p52oCYcEYjB0eWsy2o2kyM7U7feyoPiUKIfb9mnHGODlCfUQQ6wyArb	\N	9.00	pending	\N	\N	2026-01-22 14:08:07.223283	2026-01-22 14:08:07.605496
12	ORD-20260122-000012	20	89.99	9.00	80.99	0.00	stripe	pending	\N	cs_test_a1PlLULgWuRxpHPW3oNhANhYn15azm4OD6hv2pf0jgJbe9QCPtPYsfhSDD	\N	9.00	pending	\N	\N	2026-01-22 14:13:40.113141	2026-01-22 14:13:40.474629
13	ORD-20260122-000013	20	89.99	9.00	80.99	0.00	stripe	pending	\N	cs_test_a1N4EggI2caTx52qU74YQREVup3OrjtYpBU3b3ar1okKAFZGLtwg7Xfk9b	\N	9.00	pending	\N	\N	2026-01-22 14:40:59.274275	2026-01-22 14:40:59.883464
14	ORD-20260122-000014	20	89.99	9.00	80.99	0.00	stripe	pending	\N	cs_test_a1sKMXl2simpLiKKVBP7y9Mmtw6elQINBTf5bezH7Xw4p0JJp7qEJL1JKN	\N	9.00	pending	\N	\N	2026-01-22 14:52:45.155733	2026-01-22 14:52:45.746045
15	ORD-20260123-000015	20	29.99	0.00	29.99	0.00	stripe	pending	\N	cs_test_a136juZgnUfX6t3uU4HzDzmNh9PJqpKUTQC4kbjiNzY07B7mZYLrFSbvKK	\N	0.00	pending	\N	\N	2026-01-23 08:36:54.319927	2026-01-23 08:36:55.442998
16	ORD-20260123-000016	22	39.99	0.00	39.99	0.00	stripe	pending	\N	cs_test_a1gYusxOdoIzsd0fJGhfQslVoBNU3iZptZeRBKwLSqx1fRingUoiadkWTc	\N	0.00	pending	\N	\N	2026-01-23 09:26:39.244645	2026-01-23 09:26:39.96757
17	ORD-20260123-000017	22	39.99	0.00	39.99	0.00	stripe	pending	\N	cs_test_a11cYT82MXi1XfyTdLX6uVm8axm3lZsTGVPZJof5tb5Pw6XZlkrzaSIIvf	\N	0.00	pending	\N	\N	2026-01-23 09:39:48.552154	2026-01-23 09:39:49.107029
18	ORD-20260123-000018	22	39.99	0.00	39.99	0.00	stripe	pending	\N	cs_test_a1hGyLDq6yekzXrRe3PTJBxUZeydMkC7NI4m6d5p9DYC7uTAlIO7esZFff	\N	0.00	pending	\N	\N	2026-01-23 09:40:24.003306	2026-01-23 09:40:24.355544
19	ORD-20260123-000019	22	1.00	0.00	1.00	0.00	stripe	pending	\N	cs_test_a1nDRJXafXnFLIkVpADFsfiw4indgvR6XzYfv0XJTYBYeJ6M4CMJZpqusU	\N	0.00	pending	\N	\N	2026-01-23 10:09:41.448301	2026-01-23 10:09:41.863362
20	ORD-20260123-000020	22	1.00	0.00	1.00	0.00	stripe	pending	\N	cs_test_a1TmUYsOyxYgJCh9xwI3Kub9zi9XoZB7bXlZnLkixWWPqvsvFmQB9gq1PL	\N	0.00	pending	\N	\N	2026-01-23 10:10:09.651393	2026-01-23 10:10:10.019778
21	ORD-20260123-000021	22	1.00	0.00	1.00	0.00	stripe	pending	\N	cs_test_a1V33CtnLgelnuYEWWAzJvVRQn0fKvYijbstzuQNb9Xco3bPRa0pfxc4ZG	\N	0.00	pending	\N	\N	2026-01-23 10:10:22.430247	2026-01-23 10:10:22.822172
\.


--
-- Data for Name: product_gallery; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product_gallery (id, product_id, image_url, display_order, created_at) FROM stdin;
\.


--
-- Data for Name: product_tags; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product_tags (id, product_id, tag_id, created_at) FROM stdin;
5	60	5	2026-01-20 11:06:40.745094
6	60	6	2026-01-20 11:06:40.756606
7	60	7	2026-01-20 11:06:40.763891
8	60	8	2026-01-20 11:06:40.76868
9	60	9	2026-01-20 11:06:40.775865
10	60	10	2026-01-20 11:06:40.782378
11	60	11	2026-01-20 11:06:40.788892
12	61	12	2026-01-20 11:24:20.792023
13	61	13	2026-01-20 11:24:20.80031
14	61	14	2026-01-20 11:24:20.80732
15	61	15	2026-01-20 11:24:20.814571
16	61	16	2026-01-20 11:24:20.820968
17	61	17	2026-01-20 11:24:20.827044
18	62	18	2026-01-21 13:26:35.601956
19	62	19	2026-01-21 13:26:35.623553
20	62	20	2026-01-21 13:26:35.632068
21	62	21	2026-01-21 13:26:35.639004
22	62	22	2026-01-21 13:26:35.646784
23	62	23	2026-01-21 13:26:35.652072
24	62	24	2026-01-21 13:26:35.659591
25	63	25	2026-01-22 01:26:45.308608
26	63	26	2026-01-22 01:26:45.419653
27	63	27	2026-01-22 01:26:45.42495
28	63	28	2026-01-22 01:26:45.432007
29	63	29	2026-01-22 01:26:45.437217
30	63	30	2026-01-22 01:26:45.445743
31	64	31	2026-01-22 05:04:57.698074
32	64	32	2026-01-22 05:04:57.723135
33	64	33	2026-01-22 05:04:57.731007
34	64	34	2026-01-22 05:04:57.737368
35	64	35	2026-01-22 05:04:57.743496
36	64	36	2026-01-22 05:04:57.748278
37	64	37	2026-01-22 05:04:57.755037
38	65	38	2026-01-22 05:08:02.233514
39	65	39	2026-01-22 05:08:02.242765
40	65	40	2026-01-22 05:08:02.249092
41	65	41	2026-01-22 05:08:02.25639
42	65	42	2026-01-22 05:08:02.26416
43	65	43	2026-01-22 05:08:02.270582
44	65	44	2026-01-22 05:08:02.277179
52	66	45	2026-01-23 10:07:35.334999
53	66	46	2026-01-23 10:07:35.34216
54	66	47	2026-01-23 10:07:35.347931
55	66	48	2026-01-23 10:07:35.352441
56	66	49	2026-01-23 10:07:35.356749
57	66	50	2026-01-23 10:07:35.36025
58	66	51	2026-01-23 10:07:35.363737
\.


--
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.products (id, seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_size, file_type, thumbnail_url, preview_url, demo_url, license_type, downloads, views, sales, revenue, rating_average, rating_count, status, is_featured, rejection_reason, created_at, updated_at, approved_at) FROM stdin;
61	12	1	Portfolio Créatif Minimaliste	portfolio-cr-atif-minimaliste	Template portfolio élégant pour designers et photographes. Grid masonry responsive avec animation fade-in, galerie lightbox avec zoom, page about détaillée avec timeline, formulaire de contact Ajax avec validation, et intégration réseaux sociaux. Design épuré mettant en valeur vos projets. Support multi-langues. Code W3C valide.	\N	29.99	49.99	/public/uploads/products/files/696f65e4b2408_Portfolio_Creatif_Minimaliste.zip	2449043	zip	/public/uploads/products/thumbnails/696f65e4893c1_Portfolio_Creatif_Minimaliste.png	\N	https://demo-dashboard.marketflow.com	single	0	0	0	0.00	0.00	0	approved	f	\N	2026-01-20 11:24:20.7358	2026-01-21 11:57:59.031516	2026-01-21 11:57:59.031516
60	12	1	Dashboard Admin Moderne	dashboard-admin-moderne	Template dashboard admin complet avec plus de 50 composants réutilisables. Interface moderne et responsive avec support dark mode, graphiques interactifs Chart.js, tableaux de données avancés, et système de notifications en temps réel. Inclut authentification, gestion utilisateurs, analytics, rapports PDF. Code propre et bien documenté. Compatible tous navigateurs modernes.	\N	49.99	79.99	/public/uploads/products/files/696f61c045507_dashboardAdminModerne.zip	2070456	zip	/public/uploads/products/thumbnails/696f61c0167f0_dashboardAdminModerne.png	\N	https://demo-dashboard.marketflow.com	single	0	0	0	0.00	0.00	0	approved	f	\N	2026-01-20 11:06:40.40942	2026-01-21 11:58:51.886867	2026-01-21 11:58:51.886867
62	12	2	Textures Organiques Collection	textures-organiques-collection	Collection de 40 textures organiques (bois, pierre, tissu, papier) en haute qualité. Parfait pour mockups et designs naturels. Format PSD avec calques.\r\nLicense: extended\r\n	\N	34.99	54.99	/public/uploads/products/files/6970d40b86b5b_texturesOrganiquesCollection.zip	3605438	zip	/public/uploads/products/thumbnails/6970d40b6597a_texturesOrganiquesCollection.png	\N		single	0	0	0	0.00	0.00	0	approved	f	\N	2026-01-21 13:26:35.559431	2026-01-21 13:27:45.671765	2026-01-21 13:27:45.671765
63	19	3	PHP Component Library Pro	php-component-library-pro	Bibliothèque professionnelle de 80+ composants PHP prêts pour la production, conçus pour des applications web modernes.\r\nInclut des classes PHP orientées MVC, helpers sécurisés, gestion avancée des formulaires, authentification, validation des données, pagination, CRUD dynamiques et composants UI PHP/CSS.\r\nArchitecture propre, extensible et documentée, idéale pour frameworks maison ou projets sur mesure.	\N	69.99	99.99	/public/uploads/products/files/69717cd4f41c6_php_component_library_pro.zip	1938651	zip	/public/uploads/products/thumbnails/69717cd4c5456_Php_library_pro.png	\N		single	0	0	0	0.00	0.00	0	approved	f	\N	2026-01-22 01:26:45.0033	2026-01-22 01:54:25.014117	2026-01-22 01:54:25.014117
65	21	5	Collection Photos Business HD	collection-photos-business-hd	Pack de 100 photos professionnelles : bureaux modernes, équipes de travail, technologies, startup lifestyle. Haute résolution, usage commercial inclus.	\N	34.99	54.99	/public/uploads/products/files/6971b0b22c338_collection_photos_busines_HD.zip	1776865	zip	/public/uploads/products/thumbnails/6971b0b211152_collection_photos_business_hd.png	\N		single	0	0	0	0.00	0.00	0	approved	f	\N	2026-01-22 05:08:02.183218	2026-01-22 05:17:18.823614	2026-01-22 05:17:18.823614
64	21	5	Nature et Paysage 4K	nature-et-paysage-4k	 Collection de 80 photos de nature et paysages en 4K : montagnes, forêts, océans, couchers de soleil. Parfait pour designs inspirants et méditation.\r\n	\N	39.99	59.99	/public/uploads/products/files/6971aff979ad5_nature_et_paysages_4K.zip	1775921	zip	/public/uploads/products/thumbnails/6971aff95cd79_nature_et_paysages_4K.png	\N		single	0	0	0	0.00	0.00	0	approved	f	\N	2026-01-22 05:04:57.500796	2026-01-22 05:17:26.742272	2026-01-22 05:17:26.742272
66	19	4	Formation React Avancé	formation-react-avanc	Formation vidéo complète React avec projets pratiques. 12 heures de contenu HD : Hooks avancés (useReducer, useContext, custom hooks), Context API et state management, Redux Toolkit moderne, Next.js 14 et SSR, tests avec Jest et React Testing Library. 5 projets réels : e-commerce, dashboard analytics, chat app, blog CMS, portfolio. Code source complet GitHub. Certificat de completion.	\N	1.00	149.99	/public/uploads/products/files/6971b289714e0_formation_react_avancee.zip	2336265	zip	/public/uploads/products/thumbnails/6971b2894d3dd_formation_react_avancee_2026.png	\N		single	0	0	0	0.00	0.00	0	approved	f	\N	2026-01-22 05:15:53.468743	2026-01-23 10:07:35.016782	2026-01-22 05:17:03.550538
\.


--
-- Data for Name: promo_codes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.promo_codes (id, code, type, value, min_purchase, max_uses, used_count, expires_at, is_active, created_by, created_at) FROM stdin;
1	WELCOME10	percentage	10.00	0.00	100	0	\N	t	\N	2026-01-12 04:39:30.896432
\.


--
-- Data for Name: reviews; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.reviews (id, product_id, buyer_id, order_item_id, rating, title, comment, is_verified_purchase, is_approved, seller_response, seller_responded_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: tags; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tags (id, name, slug, usage_count, created_at) FROM stdin;
1	étoile	toile	0	2026-01-14 03:25:44.421852
2	brillance	brillance	0	2026-01-14 03:25:44.436936
3	luttes	luttes	0	2026-01-14 03:25:44.442439
4	compromis	compromis	0	2026-01-14 03:25:44.448172
5	dashboard	dashboard	0	2026-01-20 11:06:40.731702
6	admin	admin	0	2026-01-20 11:06:40.753728
7	template	template	0	2026-01-20 11:06:40.760691
8	bootstrap	bootstrap	0	2026-01-20 11:06:40.766653
9	responsive	responsive	0	2026-01-20 11:06:40.772108
10	dark mode	dark-mode	0	2026-01-20 11:06:40.78003
11	charts	charts	0	2026-01-20 11:06:40.785223
12	portfolio	portfolio	0	2026-01-20 11:24:20.77954
13	creative	creative	0	2026-01-20 11:24:20.79709
14	photographer	photographer	0	2026-01-20 11:24:20.80431
15	designer	designer	0	2026-01-20 11:24:20.811241
16	minimal	minimal	0	2026-01-20 11:24:20.818507
17	gallery	gallery	0	2026-01-20 11:24:20.824762
18	textures	textures	0	2026-01-21 13:26:35.597221
19	organic	organic	0	2026-01-21 13:26:35.620287
20	wood	wood	0	2026-01-21 13:26:35.628554
21	stone	stone	0	2026-01-21 13:26:35.635559
22	fabric	fabric	0	2026-01-21 13:26:35.643544
23	mockup	mockup	0	2026-01-21 13:26:35.649532
24	natural	natural	0	2026-01-21 13:26:35.656368
25	PHP	php	0	2026-01-22 01:26:45.305907
26	MVC	mvc	0	2026-01-22 01:26:45.416572
27	CRUD	crud	0	2026-01-22 01:26:45.422684
28	Authentification	authentification	0	2026-01-22 01:26:45.428643
29	Bibliothèque	biblioth-que	0	2026-01-22 01:26:45.434278
30	Composants	composants	0	2026-01-22 01:26:45.442385
31	nature	nature	0	2026-01-22 05:04:57.693278
32	landscape	landscape	0	2026-01-22 05:04:57.718736
33	mountains	mountains	0	2026-01-22 05:04:57.728143
34	ocean	ocean	0	2026-01-22 05:04:57.734493
35	4k	4k	0	2026-01-22 05:04:57.741442
36	photography	photography	0	2026-01-22 05:04:57.745902
37	travel	travel	0	2026-01-22 05:04:57.752164
38	photos	photos	0	2026-01-22 05:08:02.220592
39	business	business	0	2026-01-22 05:08:02.239764
40	office	office	0	2026-01-22 05:08:02.24621
41	team	team	0	2026-01-22 05:08:02.252977
42	startup	startup	0	2026-01-22 05:08:02.261083
43	corporate	corporate	0	2026-01-22 05:08:02.267735
44	stock	stock	0	2026-01-22 05:08:02.273914
45	react	react	0	2026-01-22 05:15:53.500021
46	formation	formation	0	2026-01-22 05:15:53.514881
47	javascript	javascript	0	2026-01-22 05:15:53.521127
48	hooks	hooks	0	2026-01-22 05:15:53.527949
49	redux	redux	0	2026-01-22 05:15:53.53399
50	nextjs	nextjs	0	2026-01-22 05:15:53.538919
51	tutorial	tutorial	0	2026-01-22 05:15:53.545185
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, full_name, username, email, password, role, avatar_url, bio, is_active, email_verified, remember_token, last_login, created_at, updated_at, shop_name, shop_slug, shop_description, shop_logo, shop_banner, total_sales, total_earnings, total_products, rating_average, rating_count) FROM stdin;
21	Emma Laurent	emma_creative	emma@marketflow.com	$2y$12$OZ/uElpjmc1sIK9lhln2OumFvS0HIKREUKzn9sBcnFGpT.amZ3o9G	seller	\N	\N	t	f	\N	\N	2026-01-22 04:57:40.043847	2026-01-22 04:57:40.043847	Creative Assets Co	creative-assets-co	\N	\N	\N	0.00	0.00	0	0.00	0
20	Sylvie	Sylvie	sylvie@buyer.com	$2y$12$Nb14kuv8Jz/Agg4dHvhLfuvcnovtU4NcdPXQ2HMnNMoxMiF3f4ljO	buyer	\N	\N	t	f	\N	2026-01-23 07:32:16	2026-01-22 04:21:05.939655	2026-01-23 06:32:16.556513		\N	\N	\N	\N	0.00	0.00	0	0.00	0
19	Alex Martin	alex_code	alex@marketflow.com	$2y$12$2fcWRyBBdeDch.Z8.EEFF.fI9f1JGW8zpKkcFZobFdVK6ZalLNnKW	seller	\N	\N	t	f	\N	2026-01-23 10:41:46	2026-01-22 00:29:18.106785	2026-01-23 09:41:46.977586	CodeLab Studio	codelab-studio	\N	\N	\N	0.00	0.00	0	0.00	0
22	Anne	Anne	anne@market.com	$2y$12$xNUvHteK1RaMQP0dnZa7Wut5xrT8nOb.HqoeoKMRoDV1zvyWu5Qqm	buyer	\N	\N	t	f	\N	2026-01-23 11:09:07	2026-01-23 07:58:19.556072	2026-01-23 10:09:07.558657		\N	\N	\N	\N	0.00	0.00	0	0.00	0
1	Admin Principal	admin	admin@marketflow.com	$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi	admin	\N	\N	t	t	\N	2026-01-29 03:11:30	2026-01-12 04:39:31.088022	2026-01-29 02:11:30.860852	\N	\N	\N	\N	\N	0.00	0.00	0	0.00	0
12	Sarah Anderson	sarah_designs	sarah@marketflow.com	$2y$12$rsc7.VhgIWYFxYgVx283l.PzAofx2E1d6tyiH6uc7m1fY.1HAYtL6	seller	\N	\N	t	f	\N	2026-01-21 14:11:29	2026-01-20 06:55:01.168605	2026-01-21 13:11:29.528405	DesignHub by Sarah	designhub-sarah	Templates et designs modernes pour créatifs passionnés	\N	\N	0.00	0.00	0	0.00	0
\.


--
-- Data for Name: wishlist; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.wishlist (id, user_id, product_id, created_at) FROM stdin;
1	20	63	2026-01-22 04:40:00.974099
2	20	62	2026-01-22 04:48:39.197359
3	20	61	2026-01-22 04:48:46.452759
\.


--
-- Name: activity_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.activity_logs_id_seq', 1, false);


--
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categories_id_seq', 12, true);


--
-- Name: order_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.order_items_id_seq', 20, true);


--
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.orders_id_seq', 21, true);


--
-- Name: product_gallery_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_gallery_id_seq', 1, false);


--
-- Name: product_tags_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_tags_id_seq', 58, true);


--
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.products_id_seq', 66, true);


--
-- Name: promo_codes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.promo_codes_id_seq', 1, true);


--
-- Name: reviews_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.reviews_id_seq', 1, false);


--
-- Name: tags_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tags_id_seq', 51, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 22, true);


--
-- Name: wishlist_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.wishlist_id_seq', 4, true);


--
-- Name: activity_logs activity_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.activity_logs
    ADD CONSTRAINT activity_logs_pkey PRIMARY KEY (id);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: categories categories_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_slug_key UNIQUE (slug);


--
-- Name: order_items order_items_license_key_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_license_key_key UNIQUE (license_key);


--
-- Name: order_items order_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_pkey PRIMARY KEY (id);


--
-- Name: orders orders_order_number_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_order_number_key UNIQUE (order_number);


--
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- Name: product_gallery product_gallery_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_gallery
    ADD CONSTRAINT product_gallery_pkey PRIMARY KEY (id);


--
-- Name: product_tags product_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_tags
    ADD CONSTRAINT product_tags_pkey PRIMARY KEY (id);


--
-- Name: product_tags product_tags_product_id_tag_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_tags
    ADD CONSTRAINT product_tags_product_id_tag_id_key UNIQUE (product_id, tag_id);


--
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- Name: products products_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_slug_key UNIQUE (slug);


--
-- Name: promo_codes promo_codes_code_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.promo_codes
    ADD CONSTRAINT promo_codes_code_key UNIQUE (code);


--
-- Name: promo_codes promo_codes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.promo_codes
    ADD CONSTRAINT promo_codes_pkey PRIMARY KEY (id);


--
-- Name: reviews reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reviews
    ADD CONSTRAINT reviews_pkey PRIMARY KEY (id);


--
-- Name: reviews reviews_product_id_buyer_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reviews
    ADD CONSTRAINT reviews_product_id_buyer_id_key UNIQUE (product_id, buyer_id);


--
-- Name: tags tags_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_name_key UNIQUE (name);


--
-- Name: tags tags_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_pkey PRIMARY KEY (id);


--
-- Name: tags tags_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_slug_key UNIQUE (slug);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users users_shop_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_shop_slug_key UNIQUE (shop_slug);


--
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- Name: wishlist wishlist_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlist
    ADD CONSTRAINT wishlist_pkey PRIMARY KEY (id);


--
-- Name: wishlist wishlist_user_id_product_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlist
    ADD CONSTRAINT wishlist_user_id_product_id_key UNIQUE (user_id, product_id);


--
-- Name: idx_categories_parent; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_categories_parent ON public.categories USING btree (parent_id);


--
-- Name: idx_categories_slug; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_categories_slug ON public.categories USING btree (slug);


--
-- Name: idx_gallery_product; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_gallery_product ON public.product_gallery USING btree (product_id);


--
-- Name: idx_logs_action; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_action ON public.activity_logs USING btree (action);


--
-- Name: idx_logs_created; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_created ON public.activity_logs USING btree (created_at);


--
-- Name: idx_logs_user; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_logs_user ON public.activity_logs USING btree (user_id);


--
-- Name: idx_order_items_license; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_order_items_license ON public.order_items USING btree (license_key);


--
-- Name: idx_order_items_order; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_order_items_order ON public.order_items USING btree (order_id);


--
-- Name: idx_order_items_product; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_order_items_product ON public.order_items USING btree (product_id);


--
-- Name: idx_order_items_seller; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_order_items_seller ON public.order_items USING btree (seller_id);


--
-- Name: idx_orders_buyer; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_orders_buyer ON public.orders USING btree (buyer_id);


--
-- Name: idx_orders_created; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_orders_created ON public.orders USING btree (created_at);


--
-- Name: idx_orders_number; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_orders_number ON public.orders USING btree (order_number);


--
-- Name: idx_orders_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_orders_status ON public.orders USING btree (status);


--
-- Name: idx_product_tags_product; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_product_tags_product ON public.product_tags USING btree (product_id);


--
-- Name: idx_product_tags_tag; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_product_tags_tag ON public.product_tags USING btree (tag_id);


--
-- Name: idx_products_category; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_products_category ON public.products USING btree (category_id);


--
-- Name: idx_products_price; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_products_price ON public.products USING btree (price);


--
-- Name: idx_products_seller; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_products_seller ON public.products USING btree (seller_id);


--
-- Name: idx_products_slug; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_products_slug ON public.products USING btree (slug);


--
-- Name: idx_products_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_products_status ON public.products USING btree (status);


--
-- Name: idx_promo_code; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_promo_code ON public.promo_codes USING btree (code);


--
-- Name: idx_reviews_buyer; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_reviews_buyer ON public.reviews USING btree (buyer_id);


--
-- Name: idx_reviews_product; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_reviews_product ON public.reviews USING btree (product_id);


--
-- Name: idx_reviews_rating; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_reviews_rating ON public.reviews USING btree (rating);


--
-- Name: idx_tags_slug; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_tags_slug ON public.tags USING btree (slug);


--
-- Name: idx_users_email; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_users_email ON public.users USING btree (email);


--
-- Name: idx_users_role; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_users_role ON public.users USING btree (role);


--
-- Name: idx_users_shop_slug; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_users_shop_slug ON public.users USING btree (shop_slug);


--
-- Name: idx_users_username; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_users_username ON public.users USING btree (username);


--
-- Name: idx_wishlist_product; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_wishlist_product ON public.wishlist USING btree (product_id);


--
-- Name: idx_wishlist_user; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_wishlist_user ON public.wishlist USING btree (user_id);


--
-- Name: orders_with_details _RETURN; Type: RULE; Schema: public; Owner: postgres
--

CREATE OR REPLACE VIEW public.orders_with_details AS
 SELECT o.id,
    o.order_number,
    o.buyer_id,
    o.subtotal,
    o.discount,
    o.total_amount,
    o.platform_fee,
    o.payment_method,
    o.payment_status,
    o.stripe_payment_id,
    o.stripe_session_id,
    o.promo_code_id,
    o.promo_discount,
    o.status,
    o.paid_at,
    o.completed_at,
    o.created_at,
    o.updated_at,
    u.full_name AS buyer_name,
    u.email AS buyer_email,
    count(oi.id) AS items_count
   FROM ((public.orders o
     LEFT JOIN public.users u ON ((o.buyer_id = u.id)))
     LEFT JOIN public.order_items oi ON ((o.id = oi.order_id)))
  GROUP BY o.id, u.full_name, u.email;


--
-- Name: orders generate_order_number_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER generate_order_number_trigger BEFORE INSERT ON public.orders FOR EACH ROW EXECUTE FUNCTION public.generate_order_number();


--
-- Name: categories update_categories_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_categories_updated_at BEFORE UPDATE ON public.categories FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: orders update_orders_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_orders_updated_at BEFORE UPDATE ON public.orders FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: reviews update_product_rating_after_review; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_product_rating_after_review AFTER INSERT OR UPDATE ON public.reviews FOR EACH ROW EXECUTE FUNCTION public.update_product_rating();


--
-- Name: products update_products_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_products_updated_at BEFORE UPDATE ON public.products FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: reviews update_reviews_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_reviews_updated_at BEFORE UPDATE ON public.reviews FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: users update_users_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON public.users FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: activity_logs activity_logs_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.activity_logs
    ADD CONSTRAINT activity_logs_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: categories categories_parent_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_parent_id_fkey FOREIGN KEY (parent_id) REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- Name: order_items order_items_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_order_id_fkey FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: order_items order_items_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE RESTRICT;


--
-- Name: order_items order_items_seller_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_seller_id_fkey FOREIGN KEY (seller_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: orders orders_buyer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_buyer_id_fkey FOREIGN KEY (buyer_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: orders orders_promo_code_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_promo_code_id_fkey FOREIGN KEY (promo_code_id) REFERENCES public.promo_codes(id) ON DELETE SET NULL;


--
-- Name: product_gallery product_gallery_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_gallery
    ADD CONSTRAINT product_gallery_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_tags product_tags_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_tags
    ADD CONSTRAINT product_tags_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_tags product_tags_tag_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_tags
    ADD CONSTRAINT product_tags_tag_id_fkey FOREIGN KEY (tag_id) REFERENCES public.tags(id) ON DELETE CASCADE;


--
-- Name: products products_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_category_id_fkey FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- Name: products products_seller_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_seller_id_fkey FOREIGN KEY (seller_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: promo_codes promo_codes_created_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.promo_codes
    ADD CONSTRAINT promo_codes_created_by_fkey FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: reviews reviews_buyer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reviews
    ADD CONSTRAINT reviews_buyer_id_fkey FOREIGN KEY (buyer_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: reviews reviews_order_item_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reviews
    ADD CONSTRAINT reviews_order_item_id_fkey FOREIGN KEY (order_item_id) REFERENCES public.order_items(id) ON DELETE SET NULL;


--
-- Name: reviews reviews_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reviews
    ADD CONSTRAINT reviews_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: wishlist wishlist_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlist
    ADD CONSTRAINT wishlist_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: wishlist wishlist_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.wishlist
    ADD CONSTRAINT wishlist_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict trFfOqPKe6urcVZzAuqjDlEJUR1GwdtLfneVGLL5amAO7UdLTlsnnUYNxtYPtjt


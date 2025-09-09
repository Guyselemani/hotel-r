DROP DATABASE IF EXISTS gestion;

CREATE DATABASE gestion
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gestion;

/* ===========================
   TABLES RÉFÉRENTIELLES
   =========================== */
CREATE TABLE roles (
  role_id       INT PRIMARY KEY AUTO_INCREMENT,
  nom           VARCHAR(50) NOT NULL UNIQUE,
  description   VARCHAR(255)
);

CREATE TABLE personnel (
  personnel_id  INT PRIMARY KEY AUTO_INCREMENT,
  prenom        VARCHAR(80) NOT NULL,
  nom           VARCHAR(80) NOT NULL,
  email         VARCHAR(120) UNIQUE,
  telephone     VARCHAR(40),
  role_id       INT NOT NULL,
  actif         TINYINT(1) NOT NULL DEFAULT 1,
  date_embauche DATE,
  cree_le       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  maj_le        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_personnel_role FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

CREATE TABLE clients (
  client_id       INT PRIMARY KEY AUTO_INCREMENT,
  prenom          VARCHAR(80) NOT NULL,
  nom             VARCHAR(80) NOT NULL,
  email           VARCHAR(120) UNIQUE,
  telephone       VARCHAR(40),
  id_officiel     VARCHAR(60), -- CNI/Passport
  date_naissance  DATE,
  cree_le         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE types_chambres (
  type_chambre_id INT PRIMARY KEY AUTO_INCREMENT,
  code            VARCHAR(20) NOT NULL UNIQUE, -- STD, DLX, SUITE...
  libelle         VARCHAR(80) NOT NULL,
  capacite        INT NOT NULL CHECK (capacite > 0),
  prix_base       DECIMAL(10,2) NOT NULL CHECK (prix_base >= 0),
  description     TEXT
);

CREATE TABLE equipements (
  equipement_id INT PRIMARY KEY AUTO_INCREMENT,
  nom           VARCHAR(80) NOT NULL UNIQUE
);

CREATE TABLE chambres (
  chambre_id      INT PRIMARY KEY AUTO_INCREMENT,
  numero          VARCHAR(10) NOT NULL UNIQUE,
  etage           INT,
  type_chambre_id INT NOT NULL,
  statut          ENUM('DISPONIBLE','OCCUPEE','MAINTENANCE','NETTOYAGE','HORS_SERVICE') NOT NULL DEFAULT 'DISPONIBLE',
  cree_le         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_chambre_type FOREIGN KEY (type_chambre_id) REFERENCES types_chambres(type_chambre_id)
);

CREATE TABLE chambres_equipements (
  chambre_id    INT,
  equipement_id INT,
  PRIMARY KEY (chambre_id, equipement_id),
  CONSTRAINT fk_ce_chambre FOREIGN KEY (chambre_id) REFERENCES chambres(chambre_id) ON DELETE CASCADE,
  CONSTRAINT fk_ce_equip FOREIGN KEY (equipement_id) REFERENCES equipements(equipement_id) ON DELETE CASCADE
);

CREATE TABLE saisons (
  saison_id   INT PRIMARY KEY AUTO_INCREMENT,
  nom         VARCHAR(80) NOT NULL,
  date_debut  DATE NOT NULL,
  date_fin    DATE NOT NULL
);

CREATE TABLE tarifs (
  tarif_id        INT PRIMARY KEY AUTO_INCREMENT,
  type_chambre_id INT NOT NULL,
  saison_id       INT, -- NULL = hors saison
  jour_semaine    TINYINT, -- 0=dimanche ... 6=samedi, NULL = tous
  prix            DECIMAL(10,2) NOT NULL CHECK (prix >= 0),
  CONSTRAINT fk_tarif_type   FOREIGN KEY (type_chambre_id) REFERENCES types_chambres(type_chambre_id),
  CONSTRAINT fk_tarif_saison FOREIGN KEY (saison_id)       REFERENCES saisons(saison_id)
);

CREATE TABLE taxes (
  taxe_id        INT PRIMARY KEY AUTO_INCREMENT,
  nom            VARCHAR(80) NOT NULL,
  taux_pourcent  DECIMAL(6,3) NOT NULL CHECK (taux_pourcent >= 0)
);

/* ===========================
   RÉSERVATIONS / SÉJOURS
   =========================== */
CREATE TABLE reservations (
  reservation_id INT PRIMARY KEY AUTO_INCREMENT,
  client_id      INT NOT NULL,
  reserve_le     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  statut         ENUM('EN_ATTENTE','CONFIRMEE','ARRIVE','ANNULEE','NO_SHOW','PARTI') NOT NULL DEFAULT 'EN_ATTENTE',
  date_arrivee   DATE NOT NULL,
  date_depart    DATE NOT NULL,
  adultes        INT NOT NULL CHECK (adultes >= 1),
  enfants        INT NOT NULL DEFAULT 0 CHECK (enfants >= 0),
  notes          TEXT,
  source         ENUM('WEB','TELEPHONE','SANS_RDV','AGENCE','AUTRE') DEFAULT 'WEB',
  CONSTRAINT fk_res_client  FOREIGN KEY (client_id) REFERENCES clients(client_id),
  CONSTRAINT chk_res_dates  CHECK (date_depart > date_arrivee)
);

CREATE TABLE reservations_chambres (
  res_chambre_id  INT PRIMARY KEY AUTO_INCREMENT,
  reservation_id  INT NOT NULL,
  chambre_id      INT NOT NULL,
  tarif_nuit      DECIMAL(10,2) NOT NULL CHECK (tarif_nuit >= 0),
  CONSTRAINT fk_rc_res    FOREIGN KEY (reservation_id) REFERENCES reservations(reservation_id) ON DELETE CASCADE,
  CONSTRAINT fk_rc_chambre FOREIGN KEY (chambre_id)    REFERENCES chambres(chambre_id)
);

CREATE TABLE nuits_reservees (
  nuit_id         INT PRIMARY KEY AUTO_INCREMENT,
  res_chambre_id  INT NOT NULL,
  date_sejour     DATE NOT NULL,
  prix            DECIMAL(10,2) NOT NULL CHECK (prix >= 0),
  CONSTRAINT uq_rc_date UNIQUE (res_chambre_id, date_sejour),
  CONSTRAINT fk_nr_rc   FOREIGN KEY (res_chambre_id) REFERENCES reservations_chambres(res_chambre_id) ON DELETE CASCADE
);

CREATE TABLE arrivees (
  arrivee_id     INT PRIMARY KEY AUTO_INCREMENT,
  reservation_id INT NOT NULL,
  personnel_id   INT,
  arrivee_le     DATETIME NOT NULL,
  doc_verifie    TINYINT(1) NOT NULL DEFAULT 0,
  CONSTRAINT fk_arr_res  FOREIGN KEY (reservation_id) REFERENCES reservations(reservation_id),
  CONSTRAINT fk_arr_pers FOREIGN KEY (personnel_id)   REFERENCES personnel(personnel_id)
);

CREATE TABLE departs (
  depart_id      INT PRIMARY KEY AUTO_INCREMENT,
  reservation_id INT NOT NULL,
  personnel_id   INT,
  depart_le      DATETIME NOT NULL,
  CONSTRAINT fk_dep_res  FOREIGN KEY (reservation_id) REFERENCES reservations(reservation_id),
  CONSTRAINT fk_dep_pers FOREIGN KEY (personnel_id)   REFERENCES personnel(personnel_id)
);

/* ===========================
   SERVICES / MÉNAGE / MAINT.
   =========================== */
CREATE TABLE services (
  service_id     INT PRIMARY KEY AUTO_INCREMENT,
  nom            VARCHAR(80) NOT NULL,
  prix_unitaire  DECIMAL(10,2) NOT NULL CHECK (prix_unitaire >= 0)
);

CREATE TABLE commandes_services (
  commande_service_id INT PRIMARY KEY AUTO_INCREMENT,
  reservation_id      INT NOT NULL,
  service_id          INT NOT NULL,
  quantite            INT NOT NULL CHECK (quantite > 0),
  commande_le         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_cs_res     FOREIGN KEY (reservation_id) REFERENCES reservations(reservation_id) ON DELETE CASCADE,
  CONSTRAINT fk_cs_service FOREIGN KEY (service_id)     REFERENCES services(service_id)
);

CREATE TABLE menage (
  menage_id      INT PRIMARY KEY AUTO_INCREMENT,
  chambre_id     INT NOT NULL,
  personnel_id   INT,
  statut         ENUM('PROPRE','SALE','EN_COURS') NOT NULL DEFAULT 'EN_COURS',
  enregistre_le  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  notes          TEXT,
  CONSTRAINT fk_mg_chambre FOREIGN KEY (chambre_id)   REFERENCES chambres(chambre_id),
  CONSTRAINT fk_mg_pers    FOREIGN KEY (personnel_id) REFERENCES personnel(personnel_id)
);

CREATE TABLE maintenance (
  ticket_id   INT PRIMARY KEY AUTO_INCREMENT,
  chambre_id  INT,
  titre       VARCHAR(120) NOT NULL,
  detail      TEXT,
  priorite    ENUM('FAIBLE','MOYENNE','ELEVEE','CRITIQUE') NOT NULL DEFAULT 'MOYENNE',
  statut      ENUM('OUVERT','EN_COURS','RESOLU','FERME')   NOT NULL DEFAULT 'OUVERT',
  ouvert_le   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ferme_le    TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT fk_maint_chambre FOREIGN KEY (chambre_id) REFERENCES chambres(chambre_id)
);

/* ===========================
   FACTURATION / PAIEMENTS
   =========================== */
CREATE TABLE moyens_paiement (
  methode_id INT PRIMARY KEY AUTO_INCREMENT,
  nom        VARCHAR(40) NOT NULL UNIQUE -- ESPECE, CARTE, VIREMENT, MOBILE
);

CREATE TABLE factures (
  facture_id     INT PRIMARY KEY AUTO_INCREMENT,
  reservation_id INT NOT NULL,
  emise_le       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  total_ht       DECIMAL(10,2) NOT NULL DEFAULT 0,
  total_taxe     DECIMAL(10,2) NOT NULL DEFAULT 0,
  total_ttc      DECIMAL(10,2) NOT NULL DEFAULT 0,
  statut         ENUM('BROUILLON','EMISE','PAYEE','ANNULEE') NOT NULL DEFAULT 'BROUILLON',
  CONSTRAINT fk_fac_res FOREIGN KEY (reservation_id) REFERENCES reservations(reservation_id)
);

CREATE TABLE lignes_facture (
  ligne_id       INT PRIMARY KEY AUTO_INCREMENT,
  facture_id     INT NOT NULL,
  type_ligne     ENUM('CHAMBRE','SERVICE','AUTRE') NOT NULL,
  description    VARCHAR(255) NOT NULL,
  quantite       DECIMAL(10,2) NOT NULL DEFAULT 1,
  prix_unitaire  DECIMAL(10,2) NOT NULL DEFAULT 0,
  taxe_id        INT,
  total_ht       DECIMAL(10,2) NOT NULL DEFAULT 0,
  total_taxe     DECIMAL(10,2) NOT NULL DEFAULT 0,
  total_ttc      DECIMAL(10,2) NOT NULL DEFAULT 0,
  CONSTRAINT fk_lf_fac  FOREIGN KEY (facture_id) REFERENCES factures(facture_id) ON DELETE CASCADE,
  CONSTRAINT fk_lf_taxe FOREIGN KEY (taxe_id)    REFERENCES taxes(taxe_id)
);

/* ===========================
   INDEXES
   =========================== */
CREATE INDEX idx_res_dates        ON reservations (date_arrivee, date_depart);
CREATE INDEX idx_chambres_statut  ON chambres(statut);
CREATE INDEX idx_factures_statut  ON factures(statut);
CREATE INDEX idx_services_nom     ON services(nom);

/* ===========================
   FONCTION TARIFAIRE
   =========================== */
DELIMITER $$
CREATE FUNCTION fn_meilleur_tarif(p_type_chambre_id INT, p_date DATE)
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
  DECLARE v_prix DECIMAL(10,2);
  DECLARE v_js TINYINT;
  SET v_js = (DAYOFWEEK(p_date)+6)%7; -- 0..6
  SELECT COALESCE(
    (SELECT t.prix
       FROM tarifs t
       LEFT JOIN saisons s ON s.saison_id = t.saison_id
      WHERE t.type_chambre_id = p_type_chambre_id
        AND (t.saison_id IS NULL OR p_date BETWEEN s.date_debut AND s.date_fin)
        AND (t.jour_semaine IS NULL OR t.jour_semaine = v_js)
      ORDER BY (t.saison_id IS NULL) ASC, (t.jour_semaine IS NULL) ASC
      LIMIT 1),
    (SELECT prix_base FROM types_chambres WHERE type_chambre_id = p_type_chambre_id)
  ) INTO v_prix;
  RETURN v_prix;
END$$
DELIMITER ;

/* ===========================
   PROCÉDURE DE RECALCUL & TRIGGERS
   =========================== */
DELIMITER $$
CREATE PROCEDURE sp_recalculer_totaux_facture(p_facture_id INT)
BEGIN
  UPDATE factures f
  LEFT JOIN (
    SELECT facture_id,
           ROUND(SUM(total_ht),2)   AS s_ht,
           ROUND(SUM(total_taxe),2) AS s_taxe,
           ROUND(SUM(total_ttc),2)  AS s_ttc
    FROM lignes_facture
    WHERE facture_id = p_facture_id
    GROUP BY facture_id
  ) x ON x.facture_id = f.facture_id
  SET f.total_ht  = COALESCE(x.s_ht,0),
      f.total_taxe = COALESCE(x.s_taxe,0),
      f.total_ttc  = COALESCE(x.s_ttc,0);
END$$

CREATE TRIGGER trg_lf_after_insert
AFTER INSERT ON lignes_facture
FOR EACH ROW
BEGIN CALL sp_recalculer_totaux_facture(NEW.facture_id); END$$

CREATE TRIGGER trg_lf_after_update
AFTER UPDATE ON lignes_facture
FOR EACH ROW
BEGIN CALL sp_recalculer_totaux_facture(NEW.facture_id); END$$

CREATE TRIGGER trg_lf_after_delete
AFTER DELETE ON lignes_facture
FOR EACH ROW
BEGIN CALL sp_recalculer_totaux_facture(OLD.facture_id); END$$
DELIMITER ;

-- Trigger DDL Statements
DELIMITER $$

USE `edm-0.4.0`$$

CREATE
DEFINER=`root`@`localhost`
TRIGGER `edm-0.4.0`.`ai_term_taxonomies`
AFTER INSERT ON `edm-0.4.0`.`term_taxonomies`
FOR EACH ROW
BEGIN
	IF NEW.parent_id <> 0 THEN
		UPDATE term_taxonomies_proxy as termTax SET childCount = (childCount + 1)
		WHERE termTax.term_taxonomy_id = NEW.parent_id;
	END IF;

END$$

CREATE
DEFINER=`root`@`localhost`
TRIGGER `edm-0.4.0`.`au_term_taxonomies`
AFTER UPDATE ON `edm-0.4.0`.`term_taxonomies`
FOR EACH ROW
BEGIN

	IF NEW.parent_id <> OLD.parent_id THEN
		IF NEW.parent_id <> 0 THEN
			
			UPDATE term_taxonomies_proxy as termTax SET childCount = (childCount + 1)
			WHERE termTax.term_taxonomy_id = NEW.parent_id; 
		END IF;
		IF OLD.parent_id <> 0 THEN
			UPDATE term_taxonomies_proxy as termTax SET childCount = (childCount - 1)
			WHERE termTax.term_taxonomy_id = OLD.parent_id; 
		END IF;
	END IF;

END$$

CREATE
DEFINER=`root`@`localhost`
TRIGGER `edm-0.4.0`.`ad_term_taxonomies`
AFTER DELETE ON `edm-0.4.0`.`term_taxonomies`
FOR EACH ROW
BEGIN
	IF OLD.parent_id <> 0 THEN
		UPDATE term_taxonomies_proxy as termTax SET childCount = (childCount - 1)
		WHERE termTax.term_taxonomy_id = OLD.parent_id; 
	END IF;
END$$


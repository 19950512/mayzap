DROP TRIGGER cad_conta_trigger ON cad_conta;
DROP FUNCTION cad_conta_procedure();

CREATE OR REPLACE FUNCTION cad_conta_procedure()
RETURNS trigger AS
$BODY$

	BEGIN

		IF (TG_OP = 'INSERT') THEN
			INSERT INTO cad_pessoas (acc_codigo) VALUES (NEW.acc_codigo);
            RETURN NEW;
		END IF;
	END;
$BODY$

LANGUAGE plpgsql VOLATILE COST 100;

CREATE TRIGGER cad_conta_trigger AFTER INSERT ON public.cad_conta FOR EACH ROW EXECUTE PROCEDURE cad_conta_procedure();

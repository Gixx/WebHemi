ALTER TABLE `webhemi_application`
    ADD `domain` VARCHAR(255) NOT NULL DEFAULT 'www.[DOMAIN]' AFTER `copyright`;

ALTER TABLE `webhemi_application`
    MODIFY `path` VARCHAR(20) NOT NULL DEFAULT '/';


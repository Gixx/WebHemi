<?php
/**
 * WebHemi
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */

namespace WebHemi\DataEntity\User;

use WebHemi\DataEntity\DataEntityInterface;

/**
 * Class UserMetaEntity
 * @package WebHemi\DataEntity\User
 *
 * @property int $userMetaId
 * @property int $userId
 * @property string $metaKey
 * @property string $metaData
 */
class UserMetaEntity implements DataEntityInterface
{
    /**
     * Exchange data to the entity.
     * @param array $data
     * @return UserMetaEntity
     */
    public function fromArray(array $data)
    {
        $this->userMetaId = isset($data['id_user_meta']) ? (int)$data['id_user_meta'] : null;
        $this->userId     = isset($data['fk_user'])      ? (int)$data['fk_user']      : null;
        $this->metaKey    = isset($data['meta_key'])     ? $data['meta_key']          : null;
        $this->metaData   = isset($data['meta_data'])    ? $data['meta_data']         : null;

        return $this;
    }

    /**
     * Represents entity in array format.
     * @return array
     */
    public function toArray()
    {
        return [
            'id_user_meta' => $this->userMetaId,
            'fk_user'      => $this->userId,
            'meta_key'     => $this->metaKey,
            'meta_data'    => $this->metaData,
        ];
    }
}

<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

/**
 * @see BxDolVote
 */
class BxDolVoteLikesQuery extends BxDolVoteQuery
{
    public function __construct(&$oModule)
    {
        parent::__construct($oModule);
    }
}

/** @} */

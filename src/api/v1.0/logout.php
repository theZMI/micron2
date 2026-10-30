<?php

UserModel::revokeApiToken(ApiAuthToken());
(new ApiResponse())->normal(true);

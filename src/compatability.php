<?php

if (!class_exists("\\Elasticsearch\\Client")) {
	class_alias("\\Elastic\\Elasticsearch\\Client", "\\Elasticsearch\\Client");
}

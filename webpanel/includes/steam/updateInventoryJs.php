<?php
    date_default_timezone_set("America/New_York");

    // Set up the files we're going to be using
    $scriptsStr = file_get_contents($_SERVER["DOCUMENT_ROOT"] .
        "/includes/steam/scripts.json");
    $csgoEnglishStr = file_get_contents($_SERVER["DOCUMENT_ROOT"] .
        "/includes/steam/csgo_english.json");
    $schemaStr = file_get_contents($_SERVER["DOCUMENT_ROOT"] .
        "/includes/steam/schema.txt");
    $scripts = json_decode($scriptsStr, true)["items_game"];
    $langTokens = json_decode($csgoEnglishStr, true)["lang"]["Tokens"];
    $schema = json_decode($schemaStr, true)["result"];

    // Output everything to the target file
    $output = getFileHeader() .
        getItemSkin() . "\n\n" .
        getItemName() . "\n\n" .
        getItemType() . "\n\n" .
        getItemOrigin() . "\n\n" .
        getItemQuality() . "\n\n" .
        getItemRarity();

    $file = fopen($_SERVER["DOCUMENT_ROOT"] . "/js/inventory.js",
        "w");
    if ($file == false) {
        echo "File could not be updated.";
    } else {
        flock($file, LOCK_EX);
        fwrite($file, $output);
        flock($file, LOCK_UN);
        fclose($file);
        echo "File successfully updated.";
    }

    // Gets the boilerplate at the top of the file used to ID Jake as
    // the creator and label the time of generation
    function getFileHeader() {
        return "/*  File generated on " . date("j F Y H:i:s (g:i A) T") .
            "\n *  using KeyValueConverter (created by Jake of SteamToolbox" .
            ") and PHP\n */\n";
    }

    // Returns skin name (e.g. Kami, Asiimov, Redline, Blue Laminate, etc.)
    function getItemSkin() {
        global $scripts;
        global $langTokens;
        // Get Paintkits (#PaintKit_aq_copper_Tag, etc.)
        $paintKits = array();
        foreach ($scripts["paint_kits"] as $key => $value) {
            if (!isset($value["description_tag"])) { continue; }
            $descTag = $value["description_tag"];
            $descTag = str_replace("#", "", $descTag);
            $descTag = strtolower($descTag);
            array_push($paintKits, array("tag"=>$descTag,
                                         "defindex"=>$key));
        }
        // Get public skin names
        $skins = array();
        foreach ($langTokens as $key => $value) {
            if (preg_match_all("/#?paintkit_.+_tag/i", $key) > 0) {
                foreach ($paintKits as $kit) {
                    if (strtolower($key) == $kit["tag"]) {
                        array_push($skins, array("name"=>$value,
                                                 "index"=>$kit["defindex"]));
                    }
                }
            }
        }
        // Generate the function string
        $function = "";
        $function .= "function getItemSkin(defindex) {\n";
        $function .= "\tswitch (defindex) {\n";
        foreach ($skins as $skin) {
            $function .= "\t\tcase " . $skin["index"] . ": return \"" .
                $skin["name"] . "\";\n";
        }
        $function .= "\t}\n";
        $function .= "\treturn \"Unknown (\" + defindex + \")\";\n";
        $function .= "}";
        // Return the function string
        return $function;
    }

    // Function to return the item name (e.g. AK-47, 5 Year Veteran Coin, etc)
    function getItemName() {
        global $schema;
        $items = array();
        foreach ($schema["items"] as $value) {
            array_push($items, array("defindex"=>$value["defindex"],
                                     "name"=>$value["item_name"]));
        }
        // Generate the function string
        $function = "";
        $function .= "function getItemName(defindex) {\n";
        $function .= "\tswitch (defindex) {\n";
        foreach ($items as $type) {
            $function .= "\t\tcase " . $type["defindex"] . ": return \"" .
                $type["name"] . "\";\n";
        }
        $function .= "\t}\n";
        $function .= "\treturn \"Unknown (\" + defindex + \")\";\n";
        $function .= "}";
        // Return the function string
        return $function;
    }

    function getItemType() {
        global $schema;
        $types = array();
        foreach ($schema["items"] as $value) {
            array_push($types, array("defindex"=>$value["defindex"],
                                     "type"=>$value["item_class"]));
        }
        // Generate the function string
        $function = "";
        $function .= "function getItemType(defindex) {\n";
        $function .= "\tswitch (defindex) {\n";
        foreach ($types as $type) {
            $function .= "\t\tcase " . $type["defindex"] . ": return \"" .
                $type["type"] . "\";\n";
        }
        $function .= "\t}\n";
        $function .= "\treturn \"Unknown (\" + defindex + \")\";\n";
        $function .= "}";
        // Return the function string
        return $function;
    }

    function getItemOrigin() {
        global $schema;
        $origins = array();
        foreach ($schema["originNames"] as $value) {
            array_push($origins, array("defindex"=>$value["origin"],
                                     "origin"=>$value["name"]));
        }
        // Generate the function string
        $function = "";
        $function .= "function getItemOrigin(defindex) {\n";
        $function .= "\tswitch (defindex) {\n";
        foreach ($origins as $origin) {
            $function .= "\t\tcase " . $origin["defindex"] . ": return \"" .
                $origin["origin"] . "\";\n";
        }
        $function .= "\t}\n";
        $function .= "\treturn \"Unknown (\" + defindex + \")\";\n";
        $function .= "}";
        // Return the function string
        return $function;
    }

    function getItemQuality() {
        global $schema;
        global $langTokens;
        // Generate the function string
        $function = "";
        $function .= "function getItemQuality(defindex) {\n";
        $function .= "\tswitch (defindex) {\n";
        foreach ($schema["qualities"] as $name => $defindex) {
            if (!isset($langTokens[$name])) { continue; }
            $function .= "\t\tcase " . $defindex . ": return \"" .
                $langTokens[$name] . "\";\n";
        }
        $function .= "\t}\n";
        $function .= "\treturn \"Unknown (\" + defindex + \")\";\n";
        $function .= "}";
        // Return the function string
        return $function;
    }

    // Returns rarity of the item (e.g. Mil-Spec, Extraordinary, etc.)
    function getItemRarity() {
        global $scripts;
        global $langTokens;
        // ["api"=>"common_weapon", "public"=>"Consumer Grade"]
        $publicRarity = array();
        foreach ($langTokens as $key => $value) {
            if (preg_match_all("/Rarity_/", $key) > 0) {
                $rarity = strtolower(substr($key, 7)); // Remove the "Rarity_"
                $publicRarity[$rarity] = $value;
            }
        }
        // ["rarity"=>"common", "value"=>"0"]
        $rarities = array();
        foreach ($scripts["rarities"] as $key => $value) {
            array_push($rarities, array("rarity"=>$key,
                                        "defindex"=>$value["value"]));
        }
        foreach ($rarities as $rarity) {
            if (!isset($publicRarity[$rarity["rarity"]])) { continue; }
            $final[$rarity["defindex"]] = array();
            $final[$rarity["defindex"]]["weapon"] =
                $publicRarity[$rarity["rarity"] . "_weapon"];
            $final[$rarity["defindex"]]["other"] =
                $publicRarity[$rarity["rarity"]];
        }
        // Generate the function string
        $function = "";
        $function .= "function getItemRarity(defindex, rarity) {\n";
        $function .= "\tvar isWeapon = defindex <= " .
            getHighestWeaponDefindex($scripts["items"]) . ";\n";
        $function .= "\tswitch (rarity) {\n";
        foreach ($rarities as $rarity) {
            if (!isset($final[$rarity["defindex"]])) { continue; }
            $function .= "\t\tcase " . $rarity["defindex"] .
                ": return isWeapon ? \"" .
                $final[$rarity["defindex"]]["weapon"] . "\" : \"" .
                $final[$rarity["defindex"]]["other"] . "\";\n";
        }
        $function .= "\t}\n";
        $function .= "\treturn \"Unknown (\" + defindex + \")\";\n";
        $function .= "}";
        // Return the function string
        return $function;
    }

    // Returns the highest defindex of a weapon
    // (used for determining rarity string)
    function getHighestWeaponDefindex($items) {
        $highestIndex = 0;
        foreach ($items as $index => $item) {
            if (substr($item["name"], 0, 7) == "weapon_") {
                $highestIndex = $index;
            }
        }
        return $highestIndex;
    }
?>
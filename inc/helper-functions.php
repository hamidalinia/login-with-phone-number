<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

trait Helper_Functions
{

    function lwp_activation_redirect($plugin)
    {
        if (get_option('lwp_activation_redirect', true)) {
            delete_option('lwp_activation_redirect');
            if ($plugin == plugin_basename(__FILE__)) {
                // Sanitize the URL before passing it to wp_redirect
                $redirect_url = admin_url('admin.php?page=login-with-phone-number-settings');
                // Ensure no direct output before redirecting
                wp_redirect(esc_url_raw($redirect_url)); // Using esc_url_raw for a redirect
                exit();
            }
        }
    }



    function idehweb_lwp_textdomain()
    {
        $idehweb_lwp_lang_dir = dirname(plugin_basename(__FILE__)) . '/languages/';
        $idehweb_lwp_lang_dir = apply_filters('idehweb_lwp_languages_directory', $idehweb_lwp_lang_dir);

        load_plugin_textdomain('login-with-phone-number', false, $idehweb_lwp_lang_dir);

    }



    function settings_validate($input)
    {

        return $input;
    }


    function removePhpComments($str, $preserveWhiteSpace = true)
    {
        $commentTokens = [
            \T_COMMENT,
            \T_DOC_COMMENT,
        ];
        $tokens = token_get_all($str);


        if (true === $preserveWhiteSpace) {
            $lines = explode(PHP_EOL, $str);
        }


        $s = '';
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if (in_array($token[0], $commentTokens)) {
                    if (true === $preserveWhiteSpace) {
                        $comment = $token[1];
                        $lineNb = $token[2];
                        $firstLine = $lines[$lineNb - 1];
                        $p = explode(PHP_EOL, $comment);
                        $nbLineComments = count($p);
                        if ($nbLineComments < 1) {
                            $nbLineComments = 1;
                        }
                        $firstCommentLine = array_shift($p);

                        $isStandAlone = (trim($firstLine) === trim($firstCommentLine));

                        if (false === $isStandAlone) {
                            if (2 === $nbLineComments) {
                                $s .= PHP_EOL;
                            }

                            continue; // just remove inline comments
                        }

                        // stand alone case
                        $s .= str_repeat(PHP_EOL, $nbLineComments - 1);
                    }
                    continue;
                }
                $token = $token[1];
            }

            $s .= $token;
        }
        return $s;
    }


    function get_country_code_options()
    {

        $retrun_array = [["label" => "Afghanistan (‫افغانستان‬‎) [+93]", "value" => "93", "code" => "af", "is_placeholder" => false],
            ["label" => "Albania (Shqipëri) [+355]", "value" => "355", "code" => "al", "is_placeholder" => false],
            ["label" => "Algeria (‫الجزائر‬‎) [+213]", "value" => "213", "code" => "dz", "is_placeholder" => false],
            ["label" => "American Samoa [+1684]", "value" => "1684", "code" => "as", "is_placeholder" => false],
            ["label" => "Andorra [+376]", "value" => "376", "code" => "ad", "is_placeholder" => false],
            ["label" => "Angola [+244]", "value" => "244", "code" => "ao", "is_placeholder" => false],
            ["label" => "Anguilla [+1264]", "value" => "1264", "code" => "ai", "is_placeholder" => false],
            ["label" => "Antigua and Barbuda [+1268]", "value" => "1268", "code" => "ag", "is_placeholder" => false],
            ["label" => "Argentina [+54]", "value" => "54", "code" => "ar", "is_placeholder" => false],
            ["label" => "Armenia (Հայաստան) [+374]", "value" => "374", "code" => "am", "is_placeholder" => false],
            ["label" => "Aruba [+297]", "value" => "297", "code" => "aw", "is_placeholder" => false],
            ["label" => "Australia [+61]", "value" => "61", "code" => "au", "is_placeholder" => false],
            ["label" => "Austria (Österreich) [+43]", "value" => "43", "code" => "at", "is_placeholder" => false],
            ["label" => "Azerbaijan (Azərbaycan) [+994]", "value" => "994", "code" => "az", "is_placeholder" => false],
            ["label" => "Bahamas [+1242]", "value" => "1242", "code" => "bs", "is_placeholder" => false],
            ["label" => "Bahrain (‫البحرين‬‎) [+973]", "value" => "973", "code" => "bh", "is_placeholder" => false],
            ["label" => "Bangladesh (বাংলাদেশ) [+880]", "value" => "880", "code" => "bd", "is_placeholder" => false],
            ["label" => "Barbados [+1246]", "value" => "1246", "code" => "bb", "is_placeholder" => false],
            ["label" => "Belarus (Беларусь) [+375]", "value" => "375", "code" => "by", "is_placeholder" => false],
            ["label" => "Belgium (België) [+32]", "value" => "32", "code" => "be", "is_placeholder" => false],
            ["label" => "Belize [+501]", "value" => "501", "code" => "bz", "is_placeholder" => false],
            ["label" => "Benin (Bénin) [+229]", "value" => "229", "code" => "bj", "is_placeholder" => false],
            ["label" => "Bermuda [+1441]", "value" => "1441", "code" => "bm", "is_placeholder" => false],
            ["label" => "Bhutan (འབྲུག) [+975]", "value" => "975", "code" => "bt", "is_placeholder" => false],
            ["label" => "Bolivia [+591]", "value" => "591", "code" => "bo", "is_placeholder" => false],
            ["label" => "Bosnia and Herzegovina (Босна и Херцеговина) [+387]", "value" => "387", "code" => "ba", "is_placeholder" => false],
            ["label" => "Botswana [+267]", "value" => "267", "code" => "bw", "is_placeholder" => false],
            ["label" => "Brazil (Brasil) [+55]", "value" => "55", "code" => "br", "is_placeholder" => false],
            ["label" => "British Indian Ocean Territory [+246]", "value" => "246", "code" => "io", "is_placeholder" => false],
            ["label" => "British Virgin Islands [+1284]", "value" => "1284", "code" => "vg", "is_placeholder" => false],
            ["label" => "Brunei [+673]", "value" => "673", "code" => "bn", "is_placeholder" => false],
            ["label" => "Bulgaria (България) [+359]", "value" => "359", "code" => "bg", "is_placeholder" => false],
            ["label" => "Burkina Faso [+226]", "value" => "226", "code" => "bf", "is_placeholder" => false],
            ["label" => "Burundi (Uburundi) [+257]", "value" => "257", "code" => "bi", "is_placeholder" => false],
            ["label" => "Cambodia (កម្ពុជា) [+855]", "value" => "855", "code" => "kh", "is_placeholder" => false],
            ["label" => "Cameroon (Cameroun) [+237]", "value" => "237", "code" => "cm", "is_placeholder" => false],
            ["label" => "Canada [+1]", "value" => "1", "code" => "ca", "is_placeholder" => false],
            ["label" => "Cape Verde (Kabu Verdi) [+238]", "value" => "238", "code" => "cv", "is_placeholder" => false],
            ["label" => "Caribbean Netherlands [+599]", "value" => "599", "code" => "bq", "is_placeholder" => false],
            ["label" => "Cayman Islands [+1345]", "value" => "1345", "code" => "ky", "is_placeholder" => false],
            ["label" => "Central African Republic (République centrafricaine) [+236]", "value" => "236", "code" => "cf", "is_placeholder" => false],
            ["label" => "Chad (Tchad) [+235]", "value" => "235", "code" => "td", "is_placeholder" => false],
            ["label" => "Chile [+56]", "value" => "56", "code" => "cl", "is_placeholder" => false],
            ["label" => "China (中国) [+86]", "value" => "86", "code" => "cn", "is_placeholder" => false],
            ["label" => "Christmas Island [+61]", "value" => "61", "code" => "cx", "is_placeholder" => false],
            ["label" => "Cocos (Keeling) Islands [+61]", "value" => "61", "code" => "cc", "is_placeholder" => false],
            ["label" => "Colombia [+57]", "value" => "57", "code" => "co", "is_placeholder" => false],
            ["label" => "Comoros (‫جزر القمر‬‎) [+269]", "value" => "269", "code" => "km", "is_placeholder" => false],
            ["label" => "Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo) [+243]", "value" => "243", "code" => "cd", "is_placeholder" => false],
            ["label" => "Congo (Republic) (Congo-Brazzaville) [+242]", "value" => "242", "code" => "cg", "is_placeholder" => false],
            ["label" => "Cook Islands [+682]", "value" => "682", "code" => "ck", "is_placeholder" => false],
            ["label" => "Costa Rica [+506]", "value" => "506", "code" => "cr", "is_placeholder" => false],
            ["label" => "Côte d’Ivoire [+225]", "value" => "225", "code" => "ci", "is_placeholder" => false],
            ["label" => "Croatia (Hrvatska) [+385]", "value" => "385", "code" => "hr", "is_placeholder" => false],
            ["label" => "Cuba [+53]", "value" => "53", "code" => "cu", "is_placeholder" => false],
            ["label" => "Curaçao [+599]", "value" => "599", "code" => "cw", "is_placeholder" => false],
            ["label" => "Cyprus (Κύπρος) [+357]", "value" => "357", "code" => "cy", "is_placeholder" => false],
            ["label" => "Czech Republic (Česká republika) [+420]", "value" => "420", "code" => "cz", "is_placeholder" => false],
            ["label" => "Denmark (Danmark) [+45]", "value" => "45", "code" => "dk", "is_placeholder" => false],
            ["label" => "Djibouti [+253]", "value" => "253", "code" => "dj", "is_placeholder" => false],
            ["label" => "Dominica [+1767]", "value" => "1767", "code" => "dm", "is_placeholder" => false],
            ["label" => "Dominican Republic (República Dominicana) [+1]", "value" => "1", "code" => "do", "is_placeholder" => false],
            ["label" => "Ecuador [+593]", "value" => "593", "code" => "ec", "is_placeholder" => false],
            ["label" => "Egypt (‫مصر‬‎) [+20]", "value" => "20", "code" => "eg", "is_placeholder" => false],
            ["label" => "El Salvador [+503]", "value" => "503", "code" => "sv", "is_placeholder" => false],
            ["label" => "Equatorial Guinea (Guinea Ecuatorial) [+240]", "value" => "240", "code" => "gq", "is_placeholder" => false],
            ["label" => "Eritrea [+291]", "value" => "291", "code" => "er", "is_placeholder" => false],
            ["label" => "Estonia (Eesti) [+372]", "value" => "372", "code" => "ee", "is_placeholder" => false],
            ["label" => "Ethiopia [+251]", "value" => "251", "code" => "et", "is_placeholder" => false],
            ["label" => "Falkland Islands (Islas Malvinas) [+500]", "value" => "500", "code" => "fk", "is_placeholder" => false],
            ["label" => "Faroe Islands (Føroyar) [+298]", "value" => "298", "code" => "fo", "is_placeholder" => false],
            ["label" => "Fiji [+679]", "value" => "679", "code" => "fj", "is_placeholder" => false],
            ["label" => "Finland (Suomi) [+358]", "value" => "358", "code" => "fi", "is_placeholder" => false],
            ["label" => "France [+33]", "value" => "33", "code" => "fr", "is_placeholder" => false],
            ["label" => "French Guiana (Guyane française) [+594]", "value" => "594", "code" => "gf", "is_placeholder" => false],
            ["label" => "French Polynesia (Polynésie française) [+689]", "value" => "689", "code" => "pf", "is_placeholder" => false],
            ["label" => "Gabon [+241]", "value" => "241", "code" => "ga", "is_placeholder" => false],
            ["label" => "Gambia [+220]", "value" => "220", "code" => "gm", "is_placeholder" => false],
            ["label" => "Georgia (საქართველო) [+995]", "value" => "995", "code" => "ge", "is_placeholder" => false],
            ["label" => "Germany (Deutschland) [+49]", "value" => "49", "code" => "de", "is_placeholder" => false],
            ["label" => "Ghana (Gaana) [+233]", "value" => "233", "code" => "gh", "is_placeholder" => false],
            ["label" => "Gibraltar [+350]", "value" => "350", "code" => "gi", "is_placeholder" => false],
            ["label" => "Greece (Ελλάδα) [+30]", "value" => "30", "code" => "gr", "is_placeholder" => false],
            ["label" => "Greenland (Kalaallit Nunaat) [+299]", "value" => "299", "code" => "gl", "is_placeholder" => false],
            ["label" => "Grenada [+1473]", "value" => "1473", "code" => "gd", "is_placeholder" => false],
            ["label" => "Guadeloupe [+590]", "value" => "590", "code" => "gp", "is_placeholder" => false],
            ["label" => "Guam [+1671]", "value" => "1671", "code" => "gu", "is_placeholder" => false],
            ["label" => "Guatemala [+502]", "value" => "502", "code" => "gt", "is_placeholder" => false],
            ["label" => "Guernsey [+44]", "value" => "44", "code" => "gg", "is_placeholder" => false],
            ["label" => "Guinea (Guinée) [+224]", "value" => "224", "code" => "gn", "is_placeholder" => false],
            ["label" => "Guinea-Bissau (Guiné Bissau) [+245]", "value" => "245", "code" => "gw", "is_placeholder" => false],
            ["label" => "Guyana [+592]", "value" => "592", "code" => "gy", "is_placeholder" => false],
            ["label" => "Haiti [+509]", "value" => "509", "code" => "ht", "is_placeholder" => false],
            ["label" => "Honduras [+504]", "value" => "504", "code" => "hn", "is_placeholder" => false],
            ["label" => "Hong Kong (香港) [+852]", "value" => "852", "code" => "hk", "is_placeholder" => false],
            ["label" => "Hungary (Magyarország) [+36]", "value" => "36", "code" => "hu", "is_placeholder" => false],
            ["label" => "Iceland (Ísland) [+354]", "value" => "354", "code" => "is", "is_placeholder" => false],
            ["label" => "India (भारत) [+91]", "value" => "91", "code" => "in", "is_placeholder" => false],
            ["label" => "Indonesia [+62]", "value" => "62", "code" => "id", "is_placeholder" => false],
            ["label" => "Iran (‫ایران‬‎) [+98]", "value" => "98", "code" => "ir", "is_placeholder" => false],
            ["label" => "Iraq (‫العراق‬‎) [+964]", "value" => "964", "code" => "iq", "is_placeholder" => false],
            ["label" => "Ireland [+353]", "value" => "353", "code" => "ie", "is_placeholder" => false],
            ["label" => "Isle of Man [+44]", "value" => "44", "code" => "im", "is_placeholder" => false],
            ["label" => "Israel (‫ישראל‬‎) [+972]", "value" => "972", "code" => "il", "is_placeholder" => false],
            ["label" => "Italy (Italia) [+39]", "value" => "39", "code" => "it", "is_placeholder" => false],
            ["label" => "Jamaica [+1]", "value" => "1", "code" => "jm", "is_placeholder" => false],
            ["label" => "Japan (日本) [+81]", "value" => "81", "code" => "jp", "is_placeholder" => false],
            ["label" => "Jersey [+44]", "value" => "44", "code" => "je", "is_placeholder" => false],
            ["label" => "Jordan (‫الأردن‬‎) [+962]", "value" => "962", "code" => "jo", "is_placeholder" => false],
            ["label" => "Kazakhstan (Казахстан) [+7]", "value" => "7", "code" => "kz", "is_placeholder" => false],
            ["label" => "Kenya [+254]", "value" => "254", "code" => "ke", "is_placeholder" => false],
            ["label" => "Kiribati [+686]", "value" => "686", "code" => "ki", "is_placeholder" => false],
            ["label" => "Kosovo [+383]", "value" => "383", "code" => "xk", "is_placeholder" => false],
            ["label" => "Kuwait (‫الكويت‬‎) [+965]", "value" => "965", "code" => "kw", "is_placeholder" => false],
            ["label" => "Kyrgyzstan (Кыргызстан) [+996]", "value" => "996", "code" => "kg", "is_placeholder" => false],
            ["label" => "Laos (ລາວ) [+856]", "value" => "856", "code" => "la", "is_placeholder" => false],
            ["label" => "Latvia (Latvija) [+371]", "value" => "371", "code" => "lv", "is_placeholder" => false],
            ["label" => "Lebanon (‫لبنان‬‎) [+961]", "value" => "961", "code" => "lb", "is_placeholder" => false],
            ["label" => "Lesotho [+266]", "value" => "266", "code" => "ls", "is_placeholder" => false],
            ["label" => "Liberia [+231]", "value" => "231", "code" => "lr", "is_placeholder" => false],
            ["label" => "Libya (‫ليبيا‬‎) [+218]", "value" => "218", "code" => "ly", "is_placeholder" => false],
            ["label" => "Liechtenstein [+423]", "value" => "423", "code" => "li", "is_placeholder" => false],
            ["label" => "Lithuania (Lietuva) [+370]", "value" => "370", "code" => "lt", "is_placeholder" => false],
            ["label" => "Luxembourg [+352]", "value" => "352", "code" => "lu", "is_placeholder" => false],
            ["label" => "Macau (澳門) [+853]", "value" => "853", "code" => "mo", "is_placeholder" => false],
            ["label" => "Macedonia (FYROM) (Македонија) [+389]", "value" => "389", "code" => "mk", "is_placeholder" => false],
            ["label" => "Madagascar (Madagasikara) [+261]", "value" => "261", "code" => "mg", "is_placeholder" => false],
            ["label" => "Malawi [+265]", "value" => "265", "code" => "mw", "is_placeholder" => false],
            ["label" => "Malaysia [+60]", "value" => "60", "code" => "my", "is_placeholder" => false],
            ["label" => "Maldives [+960]", "value" => "960", "code" => "mv", "is_placeholder" => false],
            ["label" => "Mali [+223]", "value" => "223", "code" => "ml", "is_placeholder" => false],
            ["label" => "Malta [+356]", "value" => "356", "code" => "mt", "is_placeholder" => false],
            ["label" => "Marshall Islands [+692]", "value" => "692", "code" => "mh", "is_placeholder" => false],
            ["label" => "Martinique [+596]", "value" => "596", "code" => "mq", "is_placeholder" => false],
            ["label" => "Mauritania (‫موريتانيا‬‎) [+222]", "value" => "222", "code" => "mr", "is_placeholder" => false],
            ["label" => "Mauritius (Moris) [+230]", "value" => "230", "code" => "mu", "is_placeholder" => false],
            ["label" => "Mayotte [+262]", "value" => "262", "code" => "yt", "is_placeholder" => false],
            ["label" => "Mexico (México) [+52]", "value" => "52", "code" => "mx", "is_placeholder" => false],
            ["label" => "Micronesia [+691]", "value" => "691", "code" => "fm", "is_placeholder" => false],
            ["label" => "Moldova (Republica Moldova) [+373]", "value" => "373", "code" => "md", "is_placeholder" => false],
            ["label" => "Monaco [+377]", "value" => "377", "code" => "mc", "is_placeholder" => false],
            ["label" => "Mongolia (Монгол) [+976]", "value" => "976", "code" => "mn", "is_placeholder" => false],
            ["label" => "Montenegro (Crna Gora) [+382]", "value" => "382", "code" => "me", "is_placeholder" => false],
            ["label" => "Montserrat [+1664]", "value" => "1664", "code" => "ms", "is_placeholder" => false],
            ["label" => "Morocco (‫المغرب‬‎) [+212]", "value" => "212", "code" => "ma", "is_placeholder" => false],
            ["label" => "Mozambique (Moçambique) [+258]", "value" => "258", "code" => "mz", "is_placeholder" => false],
            ["label" => "Myanmar (Burma) (မြန်မာ) [+95]", "value" => "95", "code" => "mm", "is_placeholder" => false],
            ["label" => "Namibia (Namibië) [+264]", "value" => "264", "code" => "na", "is_placeholder" => false],
            ["label" => "Nauru [+674]", "value" => "674", "code" => "nr", "is_placeholder" => false],
            ["label" => "Nepal (नेपाल) [+977]", "value" => "977", "code" => "np", "is_placeholder" => false],
            ["label" => "Netherlands (Nederland) [+31]", "value" => "31", "code" => "nl", "is_placeholder" => false],
            ["label" => "New Caledonia (Nouvelle-Calédonie) [+687]", "value" => "687", "code" => "nc", "is_placeholder" => false],
            ["label" => "New Zealand [+64]", "value" => "64", "code" => "nz", "is_placeholder" => false],
            ["label" => "Nicaragua [+505]", "value" => "505", "code" => "ni", "is_placeholder" => false],
            ["label" => "Niger (Nijar) [+227]", "value" => "227", "code" => "ne", "is_placeholder" => false],
            ["label" => "Nigeria [+234]", "value" => "234", "code" => "ng", "is_placeholder" => false],
            ["label" => "Niue [+683]", "value" => "683", "code" => "nu", "is_placeholder" => false],
            ["label" => "Norfolk Island [+672]", "value" => "672", "code" => "nf", "is_placeholder" => false],
            ["label" => "North Korea (조선 민주주의 인민 공화국) [+850]", "value" => "850", "code" => "kp", "is_placeholder" => false],
            ["label" => "Northern Mariana Islands [+1670]", "value" => "1670", "code" => "mp", "is_placeholder" => false],
            ["label" => "Norway (Norge) [+47]", "value" => "47", "code" => "no", "is_placeholder" => false],
            ["label" => "Oman (‫عُمان‬‎) [+968]", "value" => "968", "code" => "om", "is_placeholder" => false],
            ["label" => "Pakistan (‫پاکستان‬‎) [+92]", "value" => "92", "code" => "pk", "is_placeholder" => false],
            ["label" => "Palau [+680]", "value" => "680", "code" => "pw", "is_placeholder" => false],
            ["label" => "Palestine (‫فلسطين‬‎) [+970]", "value" => "970", "code" => "ps", "is_placeholder" => false],
            ["label" => "Panama (Panamá) [+507]", "value" => "507", "code" => "pa", "is_placeholder" => false],
            ["label" => "Papua New Guinea [+675]", "value" => "675", "code" => "pg", "is_placeholder" => false],
            ["label" => "Paraguay [+595]", "value" => "595", "code" => "py", "is_placeholder" => false],
            ["label" => "Peru (Perú) [+51]", "value" => "51", "code" => "pe", "is_placeholder" => false],
            ["label" => "Philippines [+63]", "value" => "63", "code" => "ph", "is_placeholder" => false],
            ["label" => "Poland (Polska) [+48]", "value" => "48", "code" => "pl", "is_placeholder" => false],
            ["label" => "Portugal [+351]", "value" => "351", "code" => "pt", "is_placeholder" => false],
            ["label" => "Puerto Rico [+1]", "value" => "1", "code" => "pr", "is_placeholder" => false],
            ["label" => "Qatar (‫قطر‬‎) [+974]", "value" => "974", "code" => "qa", "is_placeholder" => false],
            ["label" => "Réunion (La Réunion) [+262]", "value" => "262", "code" => "re", "is_placeholder" => false],
            ["label" => "Romania (România) [+40]", "value" => "40", "code" => "ro", "is_placeholder" => false],
            ["label" => "Russia (Россия) [+7]", "value" => "7", "code" => "ru", "is_placeholder" => false],
            ["label" => "Rwanda [+250]", "value" => "250", "code" => "rw", "is_placeholder" => false],
            ["label" => "Saint Barthélemy [+590]", "value" => "590", "code" => "bl", "is_placeholder" => false],
            ["label" => "Saint Helena [+290]", "value" => "290", "code" => "sh", "is_placeholder" => false],
            ["label" => "Saint Kitts and Nevis [+1869]", "value" => "1869", "code" => "kn", "is_placeholder" => false],
            ["label" => "Saint Lucia [+1758]", "value" => "1758", "code" => "lc", "is_placeholder" => false],
            ["label" => "Saint Martin (Saint-Martin (partie française)) [+590]", "value" => "590", "code" => "mf", "is_placeholder" => false],
            ["label" => "Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon) [+508]", "value" => "508", "code" => "pm", "is_placeholder" => false],
            ["label" => "Saint Vincent and the Grenadines [+1784]", "value" => "1784", "code" => "vc", "is_placeholder" => false],
            ["label" => "Samoa [+685]", "value" => "685", "code" => "ws", "is_placeholder" => false],
            ["label" => "San Marino [+378]", "value" => "378", "code" => "sm", "is_placeholder" => false],
            ["label" => "São Tomé and Príncipe (São Tomé e Príncipe) [+239]", "value" => "239", "code" => "st", "is_placeholder" => false],
            ["label" => "Saudi Arabia (‫المملكة العربية السعودية‬‎) [+966]", "value" => "966", "code" => "sa", "is_placeholder" => false],
            ["label" => "Senegal (Sénégal) [+221]", "value" => "221", "code" => "sn", "is_placeholder" => false],
            ["label" => "Serbia (Србија) [+381]", "value" => "381", "code" => "rs", "is_placeholder" => false],
            ["label" => "Seychelles [+248]", "value" => "248", "code" => "sc", "is_placeholder" => false],
            ["label" => "Sierra Leone [+232]", "value" => "232", "code" => "sl", "is_placeholder" => false],
            ["label" => "Singapore [+65]", "value" => "65", "code" => "sg", "is_placeholder" => false],
            ["label" => "Sint Maarten [+1721]", "value" => "1721", "code" => "sx", "is_placeholder" => false],
            ["label" => "Slovakia (Slovensko) [+421]", "value" => "421", "code" => "sk", "is_placeholder" => false],
            ["label" => "Slovenia (Slovenija) [+386]", "value" => "386", "code" => "si", "is_placeholder" => false],
            ["label" => "Solomon Islands [+677]", "value" => "677", "code" => "sb", "is_placeholder" => false],
            ["label" => "Somalia (Soomaaliya) [+252]", "value" => "252", "code" => "so", "is_placeholder" => false],
            ["label" => "South Africa [+27]", "value" => "27", "code" => "za", "is_placeholder" => false],
            ["label" => "South Korea (대한민국) [+82]", "value" => "82", "code" => "kr", "is_placeholder" => false],
            ["label" => "South Sudan (‫جنوب السودان‬‎) [+211]", "value" => "211", "code" => "ss", "is_placeholder" => false],
            ["label" => "Spain (España) [+34]", "value" => "34", "code" => "es", "is_placeholder" => false],
            ["label" => "Sri Lanka (ශ්‍රී ලංකාව) [+94]", "value" => "94", "code" => "lk", "is_placeholder" => false],
            ["label" => "Sudan (‫السودان‬‎) [+249]", "value" => "249", "code" => "sd", "is_placeholder" => false],
            ["label" => "Suriname [+597]", "value" => "597", "code" => "sr", "is_placeholder" => false],
            ["label" => "Svalbard and Jan Mayen [+47]", "value" => "47", "code" => "sj", "is_placeholder" => false],
            ["label" => "Swaziland [+268]", "value" => "268", "code" => "sz", "is_placeholder" => false],
            ["label" => "Sweden (Sverige) [+46]", "value" => "46", "code" => "se", "is_placeholder" => false],
            ["label" => "Switzerland (Schweiz) [+41]", "value" => "41", "code" => "ch", "is_placeholder" => false],
            ["label" => "Syria (‫سوريا‬‎) [+963]", "value" => "963", "code" => "sy", "is_placeholder" => false],
            ["label" => "Taiwan (台灣) [+886]", "value" => "886", "code" => "tw", "is_placeholder" => false],
            ["label" => "Tajikistan [+992]", "value" => "992", "code" => "tj", "is_placeholder" => false],
            ["label" => "Tanzania [+255]", "value" => "255", "code" => "tz", "is_placeholder" => false],
            ["label" => "Thailand (ไทย) [+66]", "value" => "66", "code" => "th", "is_placeholder" => false],
            ["label" => "Timor-Leste [+670]", "value" => "670", "code" => "tl", "is_placeholder" => false],
            ["label" => "Togo [+228]", "value" => "228", "code" => "tg", "is_placeholder" => false],
            ["label" => "Tokelau [+690]", "value" => "690", "code" => "tk", "is_placeholder" => false],
            ["label" => "Tonga [+676]", "value" => "676", "code" => "to", "is_placeholder" => false],
            ["label" => "Trinidad and Tobago [+1868]", "value" => "1868", "code" => "tt", "is_placeholder" => false],
            ["label" => "Tunisia (‫تونس‬‎) [+216]", "value" => "216", "code" => "tn", "is_placeholder" => false],
            ["label" => "Turkey (Türkiye) [+90]", "value" => "90", "code" => "tr", "is_placeholder" => false],
            ["label" => "Turkmenistan [+993]", "value" => "993", "code" => "tm", "is_placeholder" => false],
            ["label" => "Turks and Caicos Islands [+1649]", "value" => "1649", "code" => "tc", "is_placeholder" => false],
            ["label" => "Tuvalu [+688]", "value" => "688", "code" => "tv", "is_placeholder" => false],
            ["label" => "U.S. Virgin Islands [+1340]", "value" => "1340", "code" => "vi", "is_placeholder" => false],
            ["label" => "Uganda [+256]", "value" => "256", "code" => "ug", "is_placeholder" => false],
            ["label" => "Ukraine (Україна) [+380]", "value" => "380", "code" => "ua", "is_placeholder" => false],
            ["label" => "United Arab Emirates (‫الإمارات العربية المتحدة‬‎) [+971]", "value" => "971", "code" => "ae", "is_placeholder" => false],
            ["label" => "United Kingdom [+44]", "value" => "44", "code" => "gb", "is_placeholder" => false],
            ["label" => "United States [+1]", "value" => "1", "code" => "us", "is_placeholder" => false],
            ["label" => "Uruguay [+598]", "value" => "598", "code" => "uy", "is_placeholder" => false],
            ["label" => "Uzbekistan (Oʻzbekiston) [+998]", "value" => "998", "code" => "uz", "is_placeholder" => false],
            ["label" => "Vanuatu [+678]", "value" => "678", "code" => "vu", "is_placeholder" => false],
            ["label" => "Vatican City (Città del Vaticano) [+39]", "value" => "39", "code" => "va", "is_placeholder" => false],
            ["label" => "Venezuela [+58]", "value" => "58", "code" => "ve", "is_placeholder" => false],
            ["label" => "Vietnam (Việt Nam) [+84]", "value" => "84", "code" => "vn", "is_placeholder" => false],
            ["label" => "Wallis and Futuna (Wallis-et-Futuna) [+681]", "value" => "681", "code" => "wf", "is_placeholder" => false],
            ["label" => "Western Sahara (‫الصحراء الغربية‬‎) [+212]", "value" => "212", "code" => "eh", "is_placeholder" => false],
            ["label" => "Yemen (‫اليمن‬‎) [+967]", "value" => "967", "code" => "ye", "is_placeholder" => false],
            ["label" => "Zambia [+260]", "value" => "260", "code" => "zm", "is_placeholder" => false],
            ["label" => "Zimbabwe [+263]", "value" => "263", "code" => "zw", "is_placeholder" => false],
            ["label" => "Åland Islands [+358]", "value" => "358", "code" => "ax", "is_placeholder" => false]];

        return $retrun_array;
    }


    function get_country_code_by_code($target_code)
    {
        $options = $this->get_country_code_options();

        foreach ($options as $country) {
            if (strtolower($country['code']) === strtolower($target_code)) {
                return $country['value'];
            }
        }

        return null; // or return a default value like '1' for US
    }




    function setting_instructions()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        $display = 'inherit';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<div> <p>' . esc_html__('make a page and name it login, put the shortcode inside it, now you have a login page!', 'login-with-phone-number') . '</p>
		<p><code>[idehweb_lwp]</code></p>';
        echo '<p class="lwp-red">' . esc_html__('if you are logged in, we do not show you any form, so after using shortcode in a page, just check it where you are not logged in, like other browsers!', 'login-with-phone-number') . '</p>';
        echo '<div> <p>' . esc_html__('For showing metas of user for example in profile page, like: showing phone number, username, email, nicename', 'login-with-phone-number') . '</p>
		<p><code>[idehweb_lwp_metas nicename="false" username="false" phone_number="true" email="false"]</code></p>';
        echo '<div> <p>' . esc_html__('For verifying your customer email, after login/register with email, you can use this shortcode: ', 'login-with-phone-number') . '</p>
		<p><code>[idehweb_lwp_verify_email]</code></p>';
        echo '<p><a href="https://idehweb.com/product/login-with-phone-number-in-wordpress/" target="_blank" class="lwp_more_help">' . esc_html__('Need more help?', 'login-with-phone-number') . '</a></p>';
        echo '</div>';

        echo '<div><button class="lwp-merge-combine-users">' . esc_html__('Sync old Woocommerce users billing phone', 'login-with-phone-number') . '</button></div>';
    }
    function get_roles()
    {
        $editable_roles = get_editable_roles();
        foreach ($editable_roles as $role => $details) {
            $sub['role'] = esc_attr($role);
            $sub['name'] = translate_user_role($details['name']);
            $roles[] = $sub;
        }
        return $roles;
    }


    function lwp_addon_woocommerce_login($template, $template_name, $template_path)
    {
        global $woocommerce;

        $_template = $template;
//        if (!$template_path)
//            $template_path = $woocommerce->template_url;
        $plugin_path = untrailingslashit(dirname(__FILE__, 2)) . '/templates/woocommerce/';
        $template = locate_template(array($plugin_path . $template_name, $template_name), true);
        if (!$template && file_exists($plugin_path . $template_name))
            $template = $plugin_path . $template_name;
        if (!$template) $template = $_template;
        return $template;
    }

    function lwp_addon_learnpress_login($template, $template_name, $template_path)
    {
//        print_r($template);

//        global $woocommerce;
        $_template = $template;
//        if (!$template_path) $template_path = $woocommerce->template_url;
        $plugin_path = untrailingslashit(plugin_dir_path(__FILE__)) . '/templates/learnpress/';
        // Look within passed path within the theme - this is priority
        $template = locate_template(array($template_path . $template_name, $template_name));
        if (!$template && file_exists($plugin_path . $template_name)) $template = $plugin_path . $template_name;
        if (!$template) $template = $_template;
//        wp_die();
        return $template;

    }




    function lwp_pre_user_query_for_phone_number($uqi)
    {
        global $wpdb;
        $search = '';
        if (isset($uqi->query_vars['search']))
            $search = trim($uqi->query_vars['search']);

        if ($search) {
            $search = trim($search, '*');
            $the_search = '%' . $search . '%';

            $search_meta = $wpdb->prepare("
        ID IN ( SELECT user_id FROM {$wpdb->usermeta}
        WHERE ( ( meta_key='phone_number')
            AND {$wpdb->usermeta}.meta_value LIKE %s )
        )", $the_search);

            $uqi->query_where = str_replace(
                'WHERE 1=1 AND (',
                "WHERE 1=1 AND (" . $search_meta . " OR ",
                $uqi->query_where);

        }
    }


    function lwp_modify_user_table($column)
    {
        $column['phone_number'] = __('Phone number', 'login-with-phone-number');
        $column['activation_code'] = __('Activation code', 'login-with-phone-number');
        $column['registered_date'] = __('Registered date', 'login-with-phone-number');

        return $column;
    }

    function lwp_modify_user_table_row($val, $column_name, $user_id)
    {
        $udata = get_userdata($user_id);
        switch ($column_name) {
            case 'phone_number' :
                return get_the_author_meta('phone_number', $user_id);
            case 'activation_code' :
                return get_the_author_meta('activation_code', $user_id);
            case 'registered_date' :
                return $udata->user_registered;
            default:
        }
        return $val;
    }

    function lwp_make_registered_column_sortable($columns)
    {
        return wp_parse_args(array('registered_date' => 'registered'), $columns);
    }


    function my_update_cookie($logged_in_cookie)
    {
        $_COOKIE[LOGGED_IN_COOKIE] = $logged_in_cookie;
//        echo $_COOKIE[LOGGED_IN_COOKIE];
//        wp_die();
    }

    function lwp_generate_token($user_id, $contact, $send_email = false, $method = '')
    {
        $options = get_option('idehweb_lwp_settings');

        if (!isset($options['idehweb_length_of_activation_code'])) $options['idehweb_length_of_activation_code'] = '6';
//        $six_digit_random_number = wp_rand(100000, 999999);

        $digit_length = isset($options['idehweb_length_of_activation_code']) ? (int)$options['idehweb_length_of_activation_code'] : 6;
        $min = pow(10, $digit_length - 1);
        $max = pow(10, $digit_length) - 1;
        $six_digit_random_number = wp_rand($min, $max);


        update_user_meta($user_id, 'activation_code', $six_digit_random_number);
        update_user_meta($user_id, 'activation_code_timestamp', time());
        if ($send_email) {
            $wp_mail = wp_mail($contact, 'activation code', __('your activation code: ', 'login-with-phone-number') . $six_digit_random_number);
            return $wp_mail;
        } else {
            return $this->send_sms($contact, $six_digit_random_number, $method);
        }
    }




    function send_sms($phone_number, $code, $method)
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_use_custom_gateway'])) $options['idehweb_use_custom_gateway'] = '1';
        if (!isset($options['idehweb_default_gateways'])) $options['idehweb_default_gateways'] = ['custom'];

            if (!in_array($method, $options['idehweb_default_gateways'])) {
                return false;
            }
            if ($method == 'custom') {
                $custom = new LWP_CUSTOM_Api();
                return $custom->lwp_send_sms($phone_number, $code);
            } else {
                do_action('lwp_send_sms_' . $method, $phone_number, $code);
            }


    }

}
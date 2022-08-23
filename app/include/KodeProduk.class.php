<?php


class KodeProduk {
    public static function getEchoTest(){
        return "ECHO";
    }
    public static function getSignOn(){
        return "SIGNON";
    }
    public static function getSignOff(){
        return "SIGNOFF";
    }
    public static function getTelepon(){
        return "TELEPON";
    }
    public static function getFlexi(){
        return "TELFLEX";
    }
    public static function getPLNPostpaid(){
        return "PLNPASC";
    }
    public static function getPLNPostpaids() {
        $ret = array(
            "PLNPASC",
            "PLNPASCH",
            "PLNPASCB",
            "PLNPASCMOB",
            "PLNPASCM",
            "PLNPASCL",
            "PLNPASC30H",
            "PLNPASCH25"
        );
        return $ret;
    }
    public static function getPLNPrepaid(){
        return "PLNPRA";
    }
    public static function getPLNPrepaids() {
        $ret = array(
            "PLNPRAO",
            "PLNPRA",
            "PLNPRAH",
            "PLNPRAY",
            "PLNPRA30",
            "PLNPRA18",
            "PLNPRAB",
            "PLNPRAT",
            "PLNPRAW",
            "PLNPRAX",
            "PLNPRA20H",
            "PLNPRA50H",
            "PLNPRA100H",
            "PLNPR20",
            "PLNPR30",
            "PLNPR50",
            "PLNPR100",
            "PLNPR200",
            "PLNPR300",
            "PLNPR500",
            "PLNPR1000",
            "PLNPRAD20",
            "PLNPRAD30",
            "PLNPRAD50",
            "PLNPRAD100",
            "PLNPRAD200",
            "PLNPRAD300",
            "PLNPRAD500",
            "PLNPRAD1K",
            "PLNPRA30H",
        );
        return $ret;
    }
    public static function getPLNNontaglist(){
        return "PLNNON";
    }
    public static function getPLNNontaglists() {
        $ret = array(
            "PLNNON",
            "PLNNONH"
        );
        return $ret;
    }
    public static function getSemestaFN(){
        return "FNSMST";
    }
    public static function getSpeedy(){
        return "SPEEDY";
    }
    public static function getPBB(){
        return "TAXPBB";
    }
    public static function getTiketKAI(){
        return "TKTKAI";
    }
    public static function getTiketPesawat(){
        return "TKTPSWT";
    }
    public static function getAsuransiJiwaNusantara(){
        return "ASRJWNS";
    }
    public static function getWOM(){
        return "FNWOM";
    }
    public static function getBAF(){
        return "FNBAF";
    }
    public static function getMAF(){
        return "FNMAF";
    }
    public static function getMCF(){
        return "FNMEGA";
    }
    public static function getTelkomVision(){
        return "TVTLKMV";
    }
    public static function getTripaKecelakaan(){
        return "ASPKCL";
    }
    public static function getTripaKebakaran(){
        return "ASPBKR";
    }   
    public static function getAoraTV(){
        return "TVAORA";
    }
    public static function getIndovision(){
        return "TVINDVS";
    }
    public static function getCentrinTV(){
        return "TVCENTRIN";
    }
    public static function getTelkomVisionPra(){
        $ret=array(
            "TELVISPEL",
            "TLKMVPRE",
        );
        return $ret;
    }
    public static function getTelkom(){
        $ret=array(
            "TELEPON",
            "TELFLEX",
            "SPEEDY",
            "SPEEDY2",
            "TELEPON2",
            "TVTLKMV",
            "TVTLKMV2"
        );
        return $ret;
    }
    public static function getTelkomSpeedy(){
        $ret=array(
            "TELEPON",
            "SPEEDY",
            "SPEEDY2",
            "TELEPON2",
        );
        return $ret;
    }
    public static function getInternet(){
        $ret = array(
            "SPEEDY",
            "SPEEDY2",
            "INETCBN",
            "CENTRIN",
            "INETIM2",
            "RADNET",
            "INDONET",
        );
        return $ret;
    }
    public static function getPonselPostpaid(){
        $ret=array(
            "TELFLEX",
            "HPESIA",
            "HPTSEL",
            "HPTSELH",
            "HPFREN",
            "HPXL",
            "HPMTRIX",
            "HPSTAR1",
            "HP3",
            "HPSMART",
            "HPTHREE"
        );
        return $ret;
    }
    public static function getKartuKredit(){
        $ret = array(
            "KKBCA",
            "KKMANDR",
            "KKHSBC",
            "KKANZ",
            "KKAMEX",
            "KKCITI",
            "KKGE",
            "KKSTDC",
            "KKBNI",
            "KKPRMTA",
            "KKMGVSA",
            "KKBII",
            "KKCIMB",
            "KKAMPO",
            "KKDNMN",
            "KKUOBB",
            "KKBBCA",
            "KKBMANDR",
            "KKBHSBC",
            "KKBANZ",
            "KKBAMEX",
            "KKBCITI",
            "KKBGE",
            "KKBSTDC",
            "KKBBNI",
            "KKBPRMTA",
            "KKBMGVSA",
            "KKBBII",
            "KKBCIMB",
            "KKBAMPO",
            "KKBDNMN",
            "KKBUOBB",
            "KKBBRI",
            "KKBBKPN",
            "KKBDANAMON",
            "KKBMGCF",
            "KKBPANIN",
            "KKBBNI"
        );
        return $ret;
    }
    public static function getMultiFinance(){
        $ret = array(
            "FNACC",
            "FNBAF",
            "FNADIRA",
            "FNCBPL",
            "FNCBRC",
	    "FNMEGAFIN",
            "FNCLMB",
            "FNCOURT",
            "FNEAZY",
            "FNFIF",
            "FNMAF",
            "FNMEGA",
            "FNOTOKD",
            "FNPRIMA",
            "FNRBSPL",
            "FNSMST",
            "FNSTCPL",
            "FNTUNAS",
            "FNWOM",
            "FNADIRAH",
            "FNADIRAP",
            "FNHCI",
            "FNHCIP",
            "FNBIMA"
        );
        return $ret;
    }
    public static function getTVKabel(){
        $ret = array(
            "TVTOPAS",
            "TVINDVS",
            "TVTLKMV",
            "TVTLKMV2",
            "TVAORA",
            "TVCENTRIN",
            "TELVISPEL",
            "TLKMVPRE",
            "TELVISCOS",
            "TELVISFIL",
            "TELVISHEM",
            "TELVISOLA",
            "TELVISPEL",
            "TELVISPEN",
            "TVBIG",
            "TVINNOV",
            "TVORANGE",
            "TVORG50",
            "TVORG80",
            "TVORG100",
            "TVORG300",
            "TVKV50",
            "TVKV75",
            "TVKV100",
            "TVKV125",
            "TVKV150",
            "TVKV175",
            "TVKV200",
            "TVKV250",
            "TVKV300",
            "TVKV500",
            "TVKV750",
            "TVKV1000",
            "TVSKYFAM6",
            "TVSKYFAM12",
            "TVSKYFAM1",
            "TVSKYMAN1",
            "TVSKYDEL1",
            "TVSKYDEL12",
            "TVSKYMAN3",
            "TVSKYDEL3",
            "TVSKYDEL6",
            "TVSKYFAM3",
            "TVSKYMAN12",
            "TVSKYMAN6",
            "TVNEX",
            "TVFIRST",
            "TVFIRSTH"
        );
        return $ret;
    }
    public static function getAsuransi(){
        $ret = array(
            "ASRACA",
            "ASRAIA",
            "ASRMANU",
            "ASRPRU",
            "ASRAJS",
            "ASRCARY",
            "ALLIANZ",
            "ASRCMNW",
            "ASRAXA",
            "ASRDPLK",
            "ASRPSTM",
            "AURISA",
            "ASAXAM",
            "ASSNMAS",
            "ASBMASH",
            "ASRJWNS",
            "ASRBPJSKS",
            "ASRBPJSKSH",
            "ASRBPJSKSR",
            "ASRBPJSKST",
            "ASRBPJSKSQ",
            "ASRBPJSKSZ",
            "ASRBPJSKSD",
            "ASRJWS",
            "ASRTOKIOS",
            "ASRTOKIO",
            "ASRCAR",
            "ASRIFGI",
            "ASRIFG"
        );
        return $ret;
    }

    public static function getAsuransiBpjs(){
        return "ASRBPJSKS";
    }

    public static function getPAM() {
        $ret = array(
            "WAPLYJ", // jakarta palyja
            "WAAETRA",
            "WOTMLG",
            "WOBATU",
            "WATNGR",
            "WOCRBN",
            "WAKOBGR",
            "WABOGOR", // PDAM kab bogor
            "WABEKASI",// PDAM bekasi
            "WAPLMBG",
            "WAJAMBI",
            "WALAMPUNG",
            "WASDA",
            "WABJN",
            "WABGK",
            "WABONDO",
            "WASMG",
            "WASLMN",
            "WAJPR",
            "WACLCP",
            "WAKNDL",
            "WABDG",
            "WAKABTRA",// PDAM tanggerang
            "WAMLG",
            "WAKABMLG",
            "WAPLMBNG",
            "WAJAMBI",
            "WALMPNG",
            "WABONDO",
            "WABYMS",
            "WABANJAR",
            "WAPONTI",
            "WASISAM",
            "WAETRATG",
            "WASMG",
            "WAJMBR",
            "WALMJNG",
            "WAPROLING",
            "WABWANGI",
            "WAMGLG",
            "WAMJK",
            "WABAL",
            "WASITU",
            "WATAPIN",
            "WAMANADO",
            "WAKUBURAYA",
            "WABALIKPPN",
            "WAGROGOT",
            "WABERAU",
            "WAREMBANG",
            "WAPBLINGGA",
            "WASRAGEN",
            "WAWONOGIRI",
            "WASLTIGA",
            "WAMADIUN",
            "WAKABBDG",
            "WAMAKASAR",
            "WAIBANJAR",
            "WAWONOSB",
            "WABREBES",
            "WAKABSMG",
            "WAKARANGA",
            "WAKPKLNGAN",
            "WALOMBOKT",
            "WABULELENG",
            "WAGIRIMM",
            "WATEMANGG",
            "WAHLSUNGT",
            "WAGROBGAN",
            "WAPURWORE",
            "WABYL",
            "WACLCPT",
            "WAKBMN",
            "WAKBRY",
            "WABANTUL",
            "WAJPRT",
            "WASLMNT",
            "WASRKT",
            "WATUBAN",
            "WATAGUNG",
            "WAPASU",
            "WAKOSOLO",
            "WADPSR",
            "WAMEDAN",
            "WAKOPASU",
            "WADEPOK",
            "WAKLUNGK",
            "WAGARUT",
            "WASAMPANG",
            "WAKOPROB",
            "WABATAM",
            "WABLORA",
            "WABADUNG",
            "WAKPROGO",
            "WAKABBKS",
            "WAHSU",
            "WATRENGG",
            "WASUMENEP",
            "WASKHJ",
            "WAKSR",
            "WACIREBON",
            "WANGAWI",
            "WAGRESIK",
            "WABANDUNG",
            "WAKOTRA"
        );
        return $ret;
    }
    
    public static function getNewPAM() {
        $ret = array(
            "WASBY"
        );
        return $ret;
    }

    public static function getPulsa(){
        $ret = array(
            "S5", "S10", "S15", "S20", "S25", "S50", "S100", "I5", "I10", "I20", "I25", "I50", "I100", "IG5",
            "IS5", "IS10", "IS25", "XR5", "XR10", "XR25", "XR50", "XR100", "XX10", "XX50", "XX100", "AX1", "AX5", "AX10", "AX25", "AX50", "AX100",
            "C5", "C10", "C20", "C50", "C100", "E1", "E5", "E10", "E11", "E15", "E20", "E25", "E50", "E100", "F5", "F10", "F20", "F50", "F100",
            "R10", "R25", "R50", "R100", "H5", "H10", "H25", "H50", "H100", "CM5", "CM10", "CM20", "CM50", "CM100", "O5", "O10", "O20", "O50", "O100",
            "T1", "T5", "T10", "T20", "T30", "T50", "T100",
        );
        return $ret;
    }
    public static function getAdmin(){
        $ret=array(
            KodeProduk::getDeposit(),       //DEPOSIT
            KodeProduk::getGantiPIN(),      //GANTI PIN
            "DAFTAR",                       //DAFTAR SBG RETAIL
            KodeProduk::getDaftar(),        //DAFTAR SBG AGENSI
            "MARKETING",                    //DAFTAR MARKETING
            KodeProduk::getSaldo(),         //CEK SALDO
            KodeProduk::getHarga(),         //CEK HARGA VOUCHER
            KodeProduk::getTransfer(),      //TRANSFER
            "TARIK",                        //TRANSFER (ALIAS)
            KodeProduk::getBonus(),         //CEK KOMISI
            KodeProduk::getKomplain(),      //KOMPLAIN
            "GANTIHP",                      //TAMBAH HP
            KodeProduk::getCetakStruk(),    //INFO TRANSAKSI
            KodeProduk::getSetFeeAdmin(),   //SETTING FEE ADMIN
        );
        return $ret;
    }
    public static function getTiket(){
        return "TIKET";
    }
    public static function getDeposit(){
        return "DEP";
    }
    public static function getGantiPIN(){
        return "GPIN";
    }
    public static function getDaftar(){
        return "AGENSI";
    }
    public static function getSaldo(){
        return "SAL";
    }
    public static function getHarga(){
        return "CHARGA";
    }
    public static function getTransfer(){
        return "KIRIM";
    }
    public static function getBonus(){
        return "CBONUS";
    }
    public static function getKomplain(){
        return "INFO";
    }
    public static function getCetakStruk(){
        return "PRINT";
    }
    public static function getSetFeeAdmin(){
        return "SETFEE";
    }
    public static function getDelima(){
        $ret = array(
            KodeProduk::getDelimaCashIn(),
            KodeProduk::getDelimaCashOut(),
            KodeProduk::getDelimaRefund(),
            KodeProduk::getDelimaCekCashIn(),
            KodeProduk::getDelimaCekCashOut(),
            KodeProduk::getDelimaCekRefund(),
            KodeProduk::getDelimaCekTopup(),
            KodeProduk::getDelimaTopup(),
        );
        return $ret;
    }
    public static function getDelimaCashIn(){
        return "DELCI";
    }
    public static function getDelimaCashOut(){
        return "DELCO";
    }
    public static function getDelimaRefund(){
        return "DELRE";
    }
    public static function getDelimaCekCashIn(){
        return "DELCSCI";
    }
    public static function getDelimaCekCashOut(){
        return "DELCSCO";
    }
    public static function getDelimaCekRefund(){
        return "DELCSRE";
    }
    public static function getDelimaCekTopup(){
        return "DELCSTOP";
    }
    public static function getDelimaTopup(){
        return "DELTOPUP";
    }
    public static function getGame() {
        $ret = array(
            "LY10", "LY20", "LY35", "LY65", "LY175",
            "MS10", "MS20", "MS50", "MS100",
            "ZY20", "ZY50", "ZY100",
            "GS10", "GS20", "GS30", "GS50", "GS100",
        );
        return $ret;
    }
}
?>

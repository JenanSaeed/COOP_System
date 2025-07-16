-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2025 at 10:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `contract`
--

CREATE TABLE `contract` (
  `con_id` int(11) NOT NULL,
  `con_date` date NOT NULL,
  `1st_party` varchar(30) NOT NULL,
  `2nd_party` varchar(30) NOT NULL,
  `con_duration` varchar(20) NOT NULL,
  `con_starting_date` date NOT NULL,
  `program_name` varchar(50) NOT NULL,
  `program_id` int(11) NOT NULL,
  `num_weeks` int(11) NOT NULL,
  `total` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `emp_id` int(9) NOT NULL,
  `name` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `role` varchar(25) NOT NULL,
  `signature` longblob NOT NULL,
  `last_vac` date NOT NULL,
  `used_days` int(11) NOT NULL,
  `remaining_days` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`emp_id`, `name`, `password`, `role`, `signature`, `last_vac`, `used_days`, `remaining_days`, `email`, `address`, `phone`) VALUES
(111, 'مشاعل الخالدي', 'Emp_111', 'employee', 0x89504e470d0a1a0a0000000d4948445200000103000000c208030000007acc575c00000090504c5445ffffff3434342626260000002d2d2d3636363a3a3a4444443535352929293f3f3f3131314646464e4e4e4949492525255151511e1e1ef3f3f35f5f5f202020707070565656e4e4e41a1a1aaeaeaef9f9f9848484dcdcdca5a5a5ebebeb606060696969c0c0c09696968e8e8ebcbcbc0e0e0ed1d1d16d6d6d7a7a7ad3d3d3c7c7c79a9a9aaaaaaaa0a0a07f7f7fbdbdbd8c37abb80000087249444154789ceddbeb7aa2bc1600600221310909a71050101144f138f77f771bdab1553b9df67bf668ab5d6f7fd443ec13430e6b85d4b2000000000000000000000000000000000000000000000000000000000000000000000000f889265f5d812f37c93befd75757e26b6da47451157d7535be52134aaa9079ac3628d3a2483f5d3a0f54951f6873c50adddc7a2a5d1266ea93c57369388e8d091e675a5c9b8c712331f1b69f2adfd984982289c9feca15bb9d72c63d22bae90e49527ca2fc984ba3d27236aa3e3f78beb924f4422e36fda38576761f972f623e4bfab53148baabd7ed461a679419fa744523ec561f962f0f84ab7e104ce3e0339de61e243ee1f8f0fc3822e8e34bdb198f0ceb81e1d5552b7633930e6b3b3b4e6d1343f38f3eb1408167fadf734facaf5bb71b59570ee2767d7c5a20777359e462da4b436cd37e0c14427dd85c77a17698f0c3d7f53075d8f4a2c8268ccbd3e795a7869110111797d603588436d2f6f2f58502bdb9b8b96068fcfa74335341bf2658fbf841668314876d70dad7f7585ef6832413eec93c19c74af5d73ff582d9d27a0865b2380b76f72377715e6212323f797d3a96060ff9f201b3c47a4c796b5fcc897548f4c95088891926d025f5b2f94d6b763b31712e9601495ce7a5d3970745e3a1c0ca23871b57ed56269c88f30d81b4c559f0f2eca002338c95b90afe96603eff89fa3ee78b311617a37c9349fed2e91786cfe2fef79a0a31b6de51ece3d1d39bcab9522dafab4f042e42e51d56f2e5895ca96104ac55c5667ffe03c52e9148b8ed1039ccef32849ad8a3ec3c6d9ccc70f6b25554132e17c36bd2957fcc99f7412c63437d71c7bb0a1b36d2e75f6eaefdec981b469d91c3486810228b379fed150e5214f9dbf53def312692caf3570cc3e4f8b8e66e3c1ee22ad3bdd3cbc779bedfdcf7e65a9951f29c3d95eba2182fd7e952dbab633e35e1ea29af563489bfac8a57d7306a47d6646e549bf942fb023b4838c798692368bcb1263155f13df7f5bf2b9924b83ab836f728895dca910c59d61ea70365865ca9c2c47d983dc4b7728d5cea73a978ec99d561dbcc3b076be7f72a98f21549a386c6e4131b8ef76a9e21e60aea8de461513cf7f6260b68fe3c27a6231b25cbcaf7dcd7bb2a93471b136bad6d41b4bb9dbc4eec92e1aaffaac5725ff783c26f9dd6c5e6e5dd88aebea09ed733d91216da2d999f5eda52bb61becc25e288f32cc35996b5af7b4751ecb27703e63b54e010fbaccdcfd7f68460a408c71e314a676d1b668e40ea38474e390ff1e586cbfddaf7814146b83a1fdf11f139121861441a224283094dad5fb97e8e11c7926ace1fa52394391399cd18b9c8759bd617880461332efb2e8168377d6aa3f15384b420b19a8dcc7d07852f166de67a08fbf6452ab8c9421b634c9ea201c4cf93a97d60b01d862fdd209a9aedfdae12b9a35d62dbca0dce439f8d6d87021f3bbb51f4e4ed496e8c2619f58eaf2dbc1ebfd34628249728c8e50839e7f96e233466f6cbd6318df1eb37dcb881524692e3018c498302deb9d57d2e95e34cd88ca4852d5877bebf1cc6cc765ed3c398bd2e8a6313f4ddc270ea3dcf8e69d079b4ab88f0eff150cacc613ea751140a579fde3a9c5482f4eba2f7da2c35c7bf73a7b98cdb118d6795fadd2c532e9346a6d6dcc82ab9bb3932687dbbedaff55e50fb6c97b85f286c169c6e9eff22b4cd8ba2684c268961228f365c55fd1b3be5136accd01aa52194beb95bf9ad4531096cbf1fc38bcc2367e9708324d3c4a6272f95c2d75a8b56b8522ad7f4d3c49ce2b1953601ede38aea799c948a78aabea7ac32d0dcd5c3028f05724fcf52ecc9884ae2a1b3f3154bc685d09a0962b7abe12b17c2abb681c78da1f5710414496c025205bb7abadd8dc7f3328d26d6375e2d2a8688ddf5170ff7995275f2c65870ec633c0acecbcf65eb212e98bd7d6e9b89f228326486cd495b95268915761c8615c58abb6a1674f5619e7ecf76588464c486882fc6d8562753d9b2c504612db23761f0a28b93fc25a99a30ed0584c517fb8753435ac771308eb91f23c48d928852e475f57eb1fe5ec364ed60bb1dce98d6993f1227e708226e8420ae22e6fd0f3f9bb9d2992dde2c0493629b37f574335ef43ff5acf2b420462ba2a8e04152ef76f35f693934e4d927cbf2e69b114bdb4576d25763da62cc4ef384c6488f5381d0876b7dd14dd79f590ad362b1ed1a4939518627fd608915ad564d42ba6dddecf3eeb08bbdc65494ddf68c5be4092d0e7dc3ef9dfea29f260263c6953f9e799cfdebcb12959b4dddf5b3854b259f05cc0433444c20159d7549c2034fdff65efe8a619c45c3a4d00702a7173ca544f17a8389bcd69da2495a2c97bb5952058a4b83888afbde50efeb7ababbe9c99eb993e921009a672e3f3f8a978494b8962228bc7685a2282dca75fa652bc64a8f787ff5f72df37d7a5a89ad26c44e17148dea773ffc18229cd93ab2767e1f20f0d3eb5df84cf95babea83a1873871f617451be23a5d31af0f87cfbeab44cc3d0cb9a4bec704f03f19ee25cf5c4efa28e06c34362c18768d623cba3c8ff378e6adddc72d58e8f3ff455afaccd7536b99b9a4faa29add50cc5c57b8170752278a13dee708010efd479f0d7ad12af36573b12a1d341ef98535c6183ffaa2f064b2195ffee34134f27dbdb2d29061f7ee3683fe914ef859965a07dbd5777c98e8ff523a42f3dc4afdd168f63d93fdebab31d37dc8b8f2e5db7d831f221d767ec6d65873f1e1bec1a3ca6d1cb6a9859968ff78eaee07285b5b641b2bd7f8614f627f681fea4c581b6debec0784477f26859d4d53c188fd53d7452bcafa1c721d68211ef8f0e507360e97c14a684fddd79db27fa96638f4bc3e95becbc3f6ff46a799e21eff19b9d23b2a9f114f5dfe07cbcfd270213d423e2ef8c0e614312d7f6ac6fcdb8288cb2d959fa77cf84d540000000000000000000000000000000000000000000000000000000000000000000000000000ffdeff00ea9f94bbf27dd4fd0000000049454e44ae426082, '2025-07-25', 8, 22, 'mk123@gmail.com', '- المملكة العربية السعودية\r\n- المنطقة الشرقية\r\n- الدمام - حي النورس\r\n- طريق الملك فيصل الساحلي\r\n- ص.ب: 1982 الدمام 31441', '+966 13 3335660'),
(222, 'محمد القحطاني', 'Fin_222', 'finance', 0x89504e470d0a1a0a0000000d49484452000000d8000000e9080300000071d809d7000000c6504c5445ffffff000000dadada000086f4f4f8fefefffbfbfcfffffdaeaeae9f9f9fe8e8f2d4d4e6a8a8a89a9a9acacbe1ebebf37b7db6ddddebe2e3efb6b7d55555551b1b1b898989696969c4c4c4c7c8deeff0f6bebfd9000089232323b4b4b46262626c6eaeadaed09091c07576b37f80b6a5a7cb3c3e9a3435975a5ca55557a59697c36366ab3232323434340e0e0e404040cbcbcb7b7b7b767676252891a7a9cb4a4ca13a3c994c4e9f21249015198f9192c09fa0c910148ce1e1e13c3c3c8f8f8f4f4f4f292d902844231f00000e8b49444154789ced5d8b7aa23a100e05898a8a40eb055a45c57bb7b575552c765bdfffa54e02a8a081968b0dbb87ff3b672b37bf0c9999fc3399440072e4c89123478e1c3972e4c89123478e1c3972e4c89123478e1c3972fc7f20b7db22ed365c0002a9af2b09be80efad266fb3493db516a504753ed5047d18fbf9de6ac8e1bfca3cb526a582f65e43ff72e3988f0b1b8b773fd685949a940a8623c738f45aacc78dc9c9b6643d8d06a584e1ca7ddfbd38ba288d7ac8448f58a5d2a454a08c0e7ad4ee477d16026525fb8ef5723aad4a0ec9540f1f6b910503d684f71e426064c4c820b73cb5a43d8bfab875e60521e867433008e6d6e948fb1df1716d5a383f9511c180b2f158fe508bf6b078d2e223ea52d226a502d1f4b2a041a44641b0bae81da401d9701e739f835f1560d08d24283ae1e4ea42392900d646de43711ae9697e4dea9c6c8c63639f2e69d1bcbd62104e4a8324ed490b3dbfb3ae4763546b92452af189747ae097b2d7a6f81117e5e91a51718d7874335d0cfd6aa391542b18d336e9ec2803be835ffa756912696895f68493508de67fae0314147a35511c05de4942df229d8dc136d3063c7389c0889419e0c797a403a19f01136bfb471c751de9e91a3907308de47fae83b9bf87de7ce1e2971810fbb7ac47fa92ab40def9f893bae2036f25a060aa24098649125da900c54d7e33af4723f65a8026d24f2ca2c1d97304e588144f27be877206926f9a7fc0d9448ba23893e8246611e3b96b60e2b3865ec40c6e80268ee8bb0e75cb79daa04663894813894ea2dda72fd850f71ecda3a9100cd0c42c04cfa3134380e448380c644dcc421258da790eca7b2ea2060d7aa4b3598858debc11ca86187f84c124e91cb789df9eb450580b2733ef4726e435a208fdc8ef277d48fb137fd2a247507d52c2380b1de6d54499ece0427011ef3827df32303803f3d8326e1c3d712b2d0927d5499206a504c93c6ae234061d1f92684a162c0c5803e0a65c066f31b8c284a074a8c3a8930e343a3b2d43a14b9cec266263e7a720d0b3301521dfbb9a389cc779cdedcde5534236f2bfb663464c6a1a2968760089ce7e423fc004ee0c0b04b5510cb9d083eb4b67af6580d6230fbf95b15c42d450c5857839f0f159c84d2142b4c62e515ac5d41e42ed8d75e2c43cc5c8a58f69873c8e6b15838b796a757550c4c2b04e4ca3fe0410214243a9bc8edd00f3c2b10f04e78b396b4573945677229057b135465afaa75390b13a4952dedacf789a3e44db00711cdf1294334e08c1c4feb2a1a953d34207839988ec2bf69bd5cf8367e50df596624ea8538f75cf57fc1411fc79c8521815a0325ed1cf0ac8e63e815c888e9d9d9829da789c0566dfdb251a69ce4d0cbda79506a8f30ec4a3ccc875603e18be794c6eb8a7eae10f40bc776f266908045e6352fb263b471e9e7677015c5c28b389681d777a5caeb3736d93811a0104a30e9475a2a6b46d49501709f3fb81948df012f013645efd7a2243b79c74477b7adf475c53a15f2280505ee384c088989cfe3670ee8757f67bbb1a5d9d66c0ba50588993bf3c9b4c7958991b2e478aa3ce7389ba9bc72b19ec30a5b64ce43b64d3308f99fe5eb2812309e0f18d1abaf377183bad89bf499e9bba70382c532c2e8265a7e241dd1c82c3497c732fd426e6f8e47ae03c09334b04087a92fda73d3a12d765ace119e2c54623f337ef99b81e52ac4bb49ce8dfd08fd92839265114dfb61b0d97501cdf50995ae617759460c7b71be5d4048d349df02584c1ce89fe65f638ba4fe945953ca6abbc81e2dad3abed472b75c2f716948d39139d83daf1bdcc289616293292ed8c7b4fa30ecfeacc5c69c76eb20e896c414fd6b644e84bd6f91c91ba8d363c0b03d6b7ce6de0c62c05aa0952d9b8f080121b8101f3ca6a6cf92de9d0ff869601cae1c5f0fb636ab9bfbd2866e6768e9cb56845d13f80fadbf7eee3db13b37f3930c8ce943c378db650e40730fed6fcb7688da70ac988ecac3dccd8f2520c8efdc6f02ce83b23c0c5cc0c2c97468ffb0641fb72428c1b8e57bd400763cf3d67a2b0fe0c333df81ab61aa1be3724109c4bb3078b79822cf2b5300de3ad7c6f35ea850e4fdc1639c5dfc922f0eb60176cf592619297207a7a471821c798894974046fce4fba2c6370d19e6c886e10813f39525cd538c9c49cac83b2e446844ac0da1565a40777a5b53d7e34ac4c2cf0b0c12b939db95fdedbeb8cea44c2208c0665a767a170198a94d9935d6eda4226d26d989a6f37bf250ef0b2b5454d5a2997fe8c1b1ce6b5786de833b2b62dd160791496df0b3a4fdd21e20658bbd3823071dde776e7490a14851eb8883a1c9e5dddf7f03203f6d44772807bf96908ebb5d720b86d7f7b798f9393e36b7dece87dbdd15ee2c1bcbe3d895b6333b0fa12028b3d633e0aab5fdc3547e270823573de80af967e8a1f17cd01a81dc499455b37771df0fa5238eb82c2f892e3bdd50dc3aab91e5cbef7506481c5b6d563053071fb0cae32c011d5f5fcb22c687f513e0901eff10633d653d232b007e2c9e694045058da9c032233ef5f123e89bdc850f97b74e55135f11efb09956d8383d32ceb23ea6398b49d1178aab25f86a66e05d61385ce6c0a8f4674ed90129f8bf794cb3890ad1097bb0efaa48a572483db117dd6433f767656603ae4ee5dae61d5cadb584580e941465e99348c6e6b06c95d970ff34a5b4fb0e690af32abaddfdab6b14a062ee8a13a3a8b015501f24eb44894ca7057fa09ace7aa333b38582d0f0380cee19c224dc1f8714006491b13d71cc9acabb7754f4cd3de6119da6370e8c321d24c83264f84601294c31e18c4b551fad649837063cf1acd0d7623dc69d1818c5fd68662521b0223700b839146124cdab993266df6e4713416bb4ffdd4f503dc731193c8e942d905958d96119320a8a2be7367040df6e4ccd7b8c85b3b55fef6b063e4ef29d6c4ca6c60c0a88c01f87d413d849de98c6dfc5a3f9e9c6196a9b2c7e1b86c7b23694f2f762eac8903988d3a32fdb70bf39fcc8f3ed19503d196e51ebd9eb971b4d5befd41a3b888ca08cef8f1788af6621cab8d0e6b036607d701c154598ba077daaa487474f4b79e624ba3a1bd0be63c122e81ba58e23bd60e91e8f64870ad3930d5defa42efeab4b83de4f721fed8dea1ed7cf64459a92e2fd68e53d37883bb894928c9df505bf666842df35f59b8c6d5ef5af87dad660727106c0e0654c01b63723261ccd8d39a899042141170786452cf966e585360381ad83bd2a9811190e0f6efa4f693d884858136a92dfb731e78da76643b7b71ef70420886a3203a28ed2831c5a064a8031d3b7ac17b0bc4b58bea98b769d8d0de9b0ea2614d0e6a7e9b52c2032ec34c40dde2ab3ddd7b4e6645a0d9c4b23771955465831d844529676fe96157b52d16e0cd374f3b45eea48f23b7b2c939ebf3f9b06c4d3d78ecbf26b86d68d43fb029876f7f000def9665bbc99506047b7b603dd8adc2b3dd3f7e0cfdb06d4620e704c9de4d1e714c0201dece12afb9b2d7aef743a63b214758e5f703100349bd0dc58e65f8ade7a63a7e13f208cfde162068a3beea1136ed3c414d56fb1d176fe1fbc2d8495da07a6a4b053c37096a3a90ed7cbeb4049619aaccf2924626870bafd513edb81108277a8ed917f226daec40c3e6db49786e4da3b2defe8b34cb6c6a5ff684996e1ded703072fc20045fd18a199584c736d4b00bee4c91e5aee84526e52aa5f4fd5275f27e6857c6172160db5d17756c1bb773824a08be5f3116630b89e4588507141377d875bd3da2f2d1232b3ef29e1f2940da865a9874efdacfd22d4de9c7a8ac116854e35c66323c80a0ee92bc823b8c29715614f47ede77407e1c3a008987d47cd9d126e13e7ab53c043a85c56f5f68c931ae16ece5cb121b6b928bbcabe775f11636e30dcbf7077756c3ac573463cd4aca3482b1704dd48f6194b4c6ac2a5ef0a17cb330354d9443775415ee8f94a2b0e4c475cc5ade908aa4ab410be5bfa3e39005c160791f3335c8ad2930e0d09d472d6f880563ff6e518d465a6015c275e4fb745428e2b6ada9803783f91e5ca5b3910dbfa7e0eccb41ab8b9044464af3239a9ecef7448214bc1baa926c4f8113c85514574630ef10d894689068d2c80a084129b3f236ad1a3c6211c5d52105ecc7cced53e3e35462cc20e7c18f52fb453a81d2af9510a32b6e9456150d0cd85ff6fa98105c84384eef1704d5642bdde3a3a7fb8f21ce15a418ef0e69151ac18b885863a3fed45618126c6095103d2fbf8080afefd22c04ad51fc25055df7d0456d3f4f75ae784eb3806a70a8af577b6b335dfa234e29d62842a0adcc81650df6bb49da1b4451ffc1014999cd7a842537c900f909e532e06b41a333f17c7dcc6917a45f0932ddf2e6eb216839f4df0e2e0bdba15f033ddabefe4a80fa3feaeb05d2ca9f7f01834c6c1b9e36205075da6db80a203713fe4d45ece9b45b7025d09812fb11f059d87135478e1c3972e4c89123478e1c3972e4c89123478e1c3972e4c89123216efe5100e61f452ed8df865cb0bf0d8e608f2586f9533d9c7b3c7c7828b66e4f47ccaf8f9f6c5942b83df6ce309517e617fa84fe7f7f609ed07f4f4cabf9e7fd69f1cc3c3dda179e3bcce313cdc6faf1107ad5150c49f5feab55eadcdd145b4dbed15db44ab7efa54ef19969aad5eea2d3616e8a8bcf62b773fdf67efc612acc1373c7743f71db7f158bcf2fe8087d2ea1d3e8137ae3f8f25da352f96c145f3f2aad4aa351f9ac169f3b956ea7d2f409f66bf1dc29764a8bca2bd3656e7007be378b0cf3d2b9615acce28169551bccededfbcdf5e5629852b359a9344b1fa5d247f5e5e6a57adb5d34abd546159d7a642aa5e762a95a6a955ebb9fc5c66711bd84e797a762a7d1fc40d71add4ac32f18d3eadc22a15e51fb51df31374f48b02ab360989bc716d3fac32c3eaaccebeda2d3fc01c12ad5dbd26bf1eef1f5b3ca14abbffe345e1a15a68a0458a02e2b31afdd6ee7a9c254d09b663eb060a5bb87e207d3fa60907e153f99a25fb006c73c2cd093ad459159749e6f6e5eba25a6f97ed3615aadc7c54d035de8201bbbb9bbbe60dd0fa6faf1506d3c379ad5cfd7bb52e9b1f45c7ded566f5fbbcd66a3fa50fd78bd6b301f0da651fdd32c75514f3cbcbe941e1aa5c797d2e74be9d62fd85f8787cff0eb7fad605f2117ec6f432ed8df86ff00cf1e25af90b4c1130000000049454e44ae426082, '0000-00-00', 0, 0, 'mq222@gmail.com', '- المملكة العربية السعودية\r\n- المنطقة الشرقية\r\n- الدمام - حي النورس\r\n- طريق الملك فيصل الساحلي\r\n- ص.ب: 1982 الدمام 31441', '+966 13 3335660'),
(333, 'جواهر الغامدي', 'Mgr_333', 'manager', 0x89504e470d0a1a0a0000000d494844520000010e000000ba0803000000684353400000007e504c5445ffffff000000f1f1f1fbfbfbf7f7f7c8c8c8e3e3e3cfcfcfbcbcbc7c7c7c737373e6e6e6c1c1c1464646d6d6d6f8f8f8ececec878787dcdcdc5252529797973131312727279e9e9e3e3e3e6d6d6db4b4b45e5e5e8b8b8b434343a9a9a96e6e6e1a1a1a3030300c0c0c2323235555556363639b9b9b181818adadad898989fdb306bb000007e049444154789ced9dd962b23a10803b08b2b9a0022a2e75adf6fd5ff06412c0a04129470dbfcc776545711826b325a15f5f04411004411004411004411004411004411004411004411004411004916358eb91375f1cbd65d2d52d8b664cc78d41c61deb16491b6672400decfaa39fc4e939ebe504ff9cb45321630f2f7e9ef8f29bb6cbdef37489a48f6486bab06e0f0c970071f07e8134320c992ee2f3507db4cbbc89af3ef491a032160ac3c859c1ce7c9b349ab1d03b3cb8fb7b98bc4718ed30077a7c3814ba00c93b84d18d19413ca8f0b911ec5f2e4b0338c26fa5cf1900f68b4569029653f1834718bd54907f8c33cc748bd0240600ba45681226406b528f0a74000cdd323408a68e8e6e191a8441be4366d09a34bd126758ea16a1497870afe46d1de449656c58e816a1497c43d5e2a60d0c29cccaacc1d52d4293d840dbe7e3647ae4486566d0ce9938353d38e816a149c4641c12097ceb16a14900b46b8af63e2308758bd0207cd8e916a149f4a1a75b840691500626410df402dfb0d62d4283b0692a5286520e190fceba45681016cdad480c69965a66410998c4b98dab8d4b0960a35b84261135b0e7d3d13613382a4f47cfba666b1d6d1543797b34d98126bb5982ae506794fdb0b5c55d1e5ad4b100883505fe957a51edb80f308fb5f4d58733804893eb582afb81c63753861f808e19b94e0cd07fffcf72d4c9f91ae0c014e1c2f1ed02614f4e5bc1c01cc7ed469f71047cfd8fa1c5385c7ddaf89ac1ed1e8610d2b5612ecceb9dd5fe1fe19945d8a8feb7ff1fbff073fd961fc14ea8c8b8d907768e605ae1ac0ed45f5cd6853fedb67a6af851380ee635b2e51dde5521d3c3b85be1ced9507f90997ffbeef299b59671b326ce9c5cf6b2f8c5f69889b9c0acc29c25bbbfb5cb637303771ab6e6b5a22c78662270e338d8e92fde625158057406d83b5f15f685b1e05cbb058dda58951df4d9fdc01b64e5b77008cf6c682eaf1d07f3e997457263b968e81c00bdc6f9f1124b6671b5a3b371ba93052ff9bef83913321faf8b67eedabb761c7e0c13c9334d24e360de60855e75f3701b54670fb527367194954e7ce19ef86d0427dc1e9ede9233d48d7c0aae1d470285a8211b47167993c75526eedfaeb2fd5081cd6fbf3a247558f9b4ef618206b90e98f636f57e4945bf28b607bb71f1efdc38e69990a7876bd551de9aeb0ed9bd9eb9656b389996d12e1db81495c153cbcbb050aa04111c0bb662e44eca98c149a41fd3870b4ed1d9d55c943a0216b4caf2becc49f8787ee16bfd1d3c7123c1a05024d9709d5e8559446512ac441a6fc2a3d5c8df286dbdd99a03b7aabe3acbfb49cd33e0c6c185e8a5036bf8942dae9d9dec05a6b7e33d33c4c1c55d1d1f4d5ae2f32ef21dc946125637e5c15e8c31b5ebe8a63a16da40eb1bf2e76dc036f19eb3e5f728274a73e85f9771bdd4782cc86389fdc847fe7211f9793bce115f5794d4443d62cc37d43fd117e1c6d8c16ecde599e290e90abf1a3d21f148a4dcd68814ce6f929b649e9a9c1e4435167e98805bf62a18a50fcf119a0cb23beebb33d53343c25c7363a573b444e98461b8cbd4e1f0cfb341d539c2cead19c50ac8f308b66ab477854374a4633f0f822c06e3291a07bfd7305bef534be9656a4125c5fc95116e33bdf4e6f8694fb8714b691d0b9e1ff578040f859e574f9d5c9762ec5a79435ceeb413c9779a37cef6fa1b2c52c52c61e402bb3e5e1a1f6f4c8dfc85950ffc80ebcb0aecf548d87b3f9366a0f21d1dfecb2351da89b33c776e7d7a89b11e448a125954f66b59b8396cee9d728132335b5ff17b8da78c78a4ee4c52f131e8a0217c051e14f12e0661a874ce1c6892a0bd71419d7effe7b95dd4ee25424cd4fe2044dff523bbc2b1d22f5a47615966c4ab3671a1112f3b7b682df8356e12987046634ca63033d9f3cbe31c8ba17ba2706386ec875e409c2758fb920180a3745950c0242fa4dddcb48c493a1e2c910c985cecf46884e3628d5a00982cd35c75947fa21bc3ca4d6e86a9f1ab18b96871fb67b84c25a3ac0a1c97b525d62cf1730bc37890f5c5ec4bdac913212c7b4669789d4ab19569c84175c1597c4c38210c0efd3f37864cf7f0baa7cde4fbfe7aa529ff096cb798811ed2c02c8a6c1e61d01f6ed8581fb0db2f4ec4d4b20bf22fc4e853673e96229065f95f76f8b2bb5c8f4ed6399f425c123813d87ac50e4eda17ebb22b8f36223a6cd9b51a627840244e642ce65936971ec0618123a9dfd87da8c7740c8cca2776f6782585eac8e59e63c91d9a8b5398bdd419ac4b9c1c2fb54ec212dcb8b98f8a4952d77d2cef79f222ba78146dc56666118ff9f83f6153430ce7f0b4509a7ff44f3cad2f101556a77fa736c6875416db64cc16a61e648d0c1e4eb70f1ca2396d98935032e34d7b23bed3db75f2543ac7159e200b431e6c3f63c965c80b32ff6edf828d89ebeef4861721521ff935d2bd1bd1f251160639e81caf0efb2f4d0ab5215a3ee5e906e79257e68c6f35f409cc718c24f79fbf19aa66cfd7ee07eeeb48b05c5bc3e9dea561b9d48ec5c701d6174bd8def583ee470e0b1511abaedc47cd5fbb2d1b25b1bf35a74d09290e445f0b2d0bbd9a08731cc19cb6936744b0dbfd0b35d57be045073dc126c5216d48743fb3e6a8092b55a0e2339fdbc0e4bad5d76a7e491b120e6943624c5e54c2a4082bb3bd9e22683587f255ab2dc4d3b84fa6798440cf27b970fedb3e990f0767cedbf43fabeed38576fccf998a58a48d02830f994925088220088220088220088220088220088220088220088220088220088220889afc077f1349b8fdbd13210000000049454e44ae426082, '0000-00-00', 0, 0, 'jq333@gmail.com', '- المملكة العربية السعودية\r\n- المنطقة الشرقية\r\n- الدمام - حي النورس\r\n- طريق الملك فيصل الساحلي\r\n- ص.ب: 1982 الدمام 31441', '+966 13 3335660');

-- --------------------------------------------------------

--
-- Table structure for table `guest`
--

CREATE TABLE `guest` (
  `guest_id` int(10) NOT NULL,
  `guest_password` varchar(30) NOT NULL,
  `guest_name` varchar(30) NOT NULL,
  `guest_email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

CREATE TABLE `terms` (
  `con_type` varchar(50) NOT NULL,
  `con_terms` text NOT NULL,
  `extra_terms` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `terms`
--

INSERT INTO `terms` (`con_type`, `con_terms`, `extra_terms`) VALUES
('(عقد تقديم خدمات (الدبلومات المهنية', '- يلتزم الطرف الثاني بتأدية مهامه الموكلة إليه من الطرف الأول بأمانة وإتقان. \n\n- يلتزم الطرف بمراجعة الوثائق. \n\n- للطرف الأول الحق في الاحتفاظ بالمحتوى التدريبي المقدم إليه من الطرف الثاني وتوظيفها وفق ما هو مناسب مع الاحتفاظ بحقوق الملكية الفكرية للطرف الثاني. \n\n- يلتزم الطرف الثاني بالمواعيد الخاصة بتسليم الاعمال المطلوبة ولا يحق له تعديل هذه المواعيد إلا بعد موافقة إدارة المركز. \n\n- يحق لمركز التعليم المستمر إلغاء العقد في حال تأخر الطرف الثاني في تسليم الأعمال المطلوبة في موعدها المحدد من قبل إدارة المركز. \n\n- يحصل الطرف الثاني على المقابل المادي المتفق عليه بالكامل في حالة حصوله على تقدير جيد أو أكثر بناءً على الواقع العلمي. \n\n- لا يتم صرف مستحقات الطرف الثاني قبل قيامه باستكمال جميع أعمال مراجعة الوثائق. \n\n- يتسلم الطرف الثاني نسخة مصدقة من هذا العقد والأصل للمركز. \n\n- يلتزم الطرف الأول بصرف مستحقات الطرف الثاني بالتحويل لحسابه في البنك المذكور أعلاه في رقم الحساب المذكور أعلاه. ', ''),
('عقد التدريب (الدورات)', '- يلتزم الطرف الثاني بتأدية مهامه الموكلة إليه بأمانة وإخلاص وإتقان. \r\n\r\n- يلتزم الطرف الثاني الخطة التدريبية قبل بداية البرنامج بأسبوعين وتتضمن خطة وطرق وأساليب التنفيذ وفقاً للنماذج المعدة من قبل المركز. \r\n\r\n- للطرف الأول الحق في الاحتفاظ بالمحتوى التدريبي المقدم إليه من الطرف الثاني وتوظيفه وفقاً للخطط التدريبية بعد موافقة الطرف الثاني مع الاحتفاظ بحقوق الملكية الفكرية للطرف الثاني. \r\n\r\n- يلتزم الطرف الثاني بالمواعيد الخاصة بالبرنامج ولا يحق له تعديل هذه المواعيد إلا بعد موافقة إدارة المركز. \r\n\r\n- تحال كافة الأعذار ومشاكل المتدربين إلى ادارة المركز للبت فيها واتخاذ الإجراء اللازم على أن يتم تسجيل المتدرب غياب في جميع الاحوال.\r\n\r\n- يحق لمركز التعليم المستمر إلغاء العقد عند غياب الطرف الثاني عن البرنامج دون إشعار إدارة المركز رسمياً بيوم واحد على الأقل، وبحد أقصى ثلاث مرات، ولا يستحق الطرف الثاني أية مستحقات مالية عن التدريب. \r\n\r\n- يتم صرف المستحقات المالية للطرف الثاني مقابل البرامج المنفذة فعلياً ويتم الحسم من المستحقات في حال التغيب عنها، وذلك من واقع كشوف الحضور الخاصة بالمشاركين في البرنامج التدريبي. \r\n\r\n- يحصل المدرب على مستحقاته المالية المتفق عليها بالكامل في حالة حصوله على تقدير جيد أو أكثر بناءً على الواقع العلمي لآراء وانطباعات المتدربين. \r\n\r\n- لا يتم صرف مستحقات الطرف الثاني قبل قيامه باستكمال جميع أعمال التدريب.  \r\n\r\n- يتسلم الطرف الثاني نسخة مصدقة من هذا العقد والأصل للمركز. \r\n\r\n- يلتزم الطرف الأول بصرف مستحقات الطرف الثاني بالتحويل لحسابه في البنك المذكور أعلاه في رقم الحساب المذكور أعلاه.', ''),
('عقد تدريب بنظام المكافأة الشهرية', '- يلتزم الطرف الثاني بتأدية مهامه الموكلة إليه بأمانة وإخلاص وإتقان. \r\n\r\n- يلتزم الطرف الثاني الخطة التدريبية قبل بداية البرنامج بأسبوعين وتتضمن خطة وطرق وأساليب التنفيذ وفقاً للنماذج المعدة من قبل المركز. \r\n\r\n- للطرف الأول الحق في الاحتفاظ بالمحتوى التدريبي المقدم إليه من الطرف الثاني وتوظيفه وفقاً للخطط التدريبية بعد موافقة الطرف الثاني مع الاحتفاظ بحقوق الملكية الفكرية للطرف الثاني. \r\n\r\n- يلتزم الطرف الثاني بالمواعيد الخاصة بالبرنامج ولا يحق له تعديل هذه المواعيد إلا بعد موافقة إدارة المركز. \r\n\r\n- تحال كافة الأعذار ومشاكل المتدربين إلى ادارة المركز للبت فيها واتخاذ الإجراء اللازم على أن يتم تسجيل المتدرب غياب في جميع الاحوال. \r\n\r\n- يلتزم الطرف الأول بصرف المستحقات المالية للطرف الثاني بواقع المبلغ المذكور ريال عن كل شهر خلال فترة سريان العقد شريطة الانتهاء من الأعمال الموكلة للطرف الثاني بموجب إفادة الإنجاز الصادرة من الجهة المختصة بالمركز.\r\n\r\n- حق لمركز التعليم المستمر إلغاء العقد عند غياب الطرف الثاني عن البرنامج دون إشعار إدارة المركز رسمياً بيوم واحد على الأقل، وبحد أقصى ثلاث مرات، ولا يستحق الطرف الثاني أية مستحقات مالية عن التدريب. \r\n\r\n- يتم صرف المستحقات المالية للطرف الثاني مقابل البرامج المنفذة فعلياً ويتم الحسم من المستحقات في حال التغيب عنها، وذلك من واقع كشوف الحضور الخاصة بالمشاركين في البرنامج التدريبي. \r\n\r\n- يحصل المدرب على مستحقاته المالية المتفق عليها بالكامل في حالة حصوله على تقدير جيد أو أكثر بناءً على الواقع العلمي لآراء وانطباعات المتدربين. \r\n\r\n- لا يتم صرف مستحقات الطرف الثاني قبل قيامه باستكمال جميع أعمال التدريب.  \r\n\r\n- يتسلم الطرف الثاني نسخة مصدقة من هذا العقد والأصل للمركز. \r\n\r\n- يلتزم الطرف الأول بصرف مستحقات الطرف الثاني بالتحويل لحسابه في البنك المذكور أعلاه في رقم الحساب المذكور أعلاه.', ''),
('عقد تقديم خدمات', '- يلتزم الطرف الثاني بتأدية مهامه وواجباته الموكلة إليه من الطرف الاول بأمانة وإتقان. \r\n\r\n- يلتزم الطرف الثاني بتصميم الحقيبة التدريبية. \r\n\r\n- يتعاون مع الطرف الأول في تحويل المحتوى التدريبي الى محتويات إلكترونية \r\n\r\n- للطرف الأول الحق في الاحتفاظ بالتقارير المقدمة إليه من الطرف الثاني وتوظيفها وفق ما هو مناسب للترجمة مع الاحتفاظ بحقوق الملكية الفكرية للطرف الثاني. \r\n\r\n- يلتزم الطرف الثاني بالمواعيد الخاصة بتسليم الاعمال المطلوبة ولا يحق له تعديل هذه المواعيد إلا بعد الرجوع لإدارة المركز. \r\n\r\n- يحق لمركز التعليم المستمر إلغاء العقد في حال تأخر الطرف الثاني في تسليم الأعمال المطلوبة في موعدها المحدد من قبل إدارة المركز.\r\n\r\n- يحصل  الطرف الثاني على المقابل المادي المتفق عليه بالكامل في حالة حصوله على تقدير جيد أو أكثر على الواقع العلمي. \r\n\r\n- لا يتم صرف مستحقات الطرف الثاني قبل قيامه باستكمال جميع أعمال  إعداد المحتوى.   \r\n\r\n- يتسلم الطرف الثاني نسخة مصدقة من هذا العقد والأصل للمركز. \r\n\r\n- يلتزم الطرف الأول بصرف مستحقات الطرف الثاني بالتحويل لحسابه في البنك المذكور أعلاه في رقم الحساب المذكور أعلاه.', ''),
('عقد عمل تعاوني (منسوبي الجامعة)', '- يلتزم الطرف الأول بتسليم نسخة من هذا العقد للطرف الثاني بعد اعتماده. \r\n\r\n- يلتزم الطرف الأول بصرف المستحقات المالية للطرف الثاني بواقع المبلغ المذكور أعلاه عن كل شهر خلال فترة سريان العقد شريطة الانتهاء من الأعمال الموكلة للطرف الثاني بموجب إفادة الإنجاز الصادرة من الجهة المختصة بالمركز. \r\n\r\n- يحق للطرف الأول تجديد وانهاء التعاقد مع الطرف الثاني بناءً على الاحتياج من عدمه. \r\n\r\n- يلتزم الطرف الثاني بالعمل لدى الطرف الأول ( مراقبة جلسات اختبار) لدى مركز التعليم المستمر وذلك بناءً على المهام والأعمال الموكلة إليه من الطرف الأول. \r\n\r\n- يلتزم الطرف الثاني بتأدية المهام الموكلة إليه من الطرف الأول بأمانة واتقان. \r\n\r\n- ما يستجد من مهام  \r\n\r\n- يلتزم الطرف الأول بصرف مستحقات الطرف الثاني بالتحويل لحسابه في البنك المذكور أعلاه في رقم الحساب المذكور أعلاه.', '');

-- --------------------------------------------------------

--
-- Table structure for table `vacation`
--

CREATE TABLE `vacation` (
  `vac_id` int(9) NOT NULL,
  `emp_id` int(9) NOT NULL,
  `type` varchar(20) NOT NULL,
  `days` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `application_date` date NOT NULL,
  `assigned_emp` varchar(30) NOT NULL,
  `fin_approval` char(10) NOT NULL,
  `man_approval` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vacation`
--

INSERT INTO `vacation` (`vac_id`, `emp_id`, `type`, `days`, `start_date`, `end_date`, `application_date`, `assigned_emp`, `fin_approval`, `man_approval`) VALUES
(2, 111, 'مرضية', 8, '2025-07-18', '2025-07-25', '2025-07-16', 'جنان', 'مقبول', 'معتمد'),
(3, 111, 'مرضية', 4, '2025-07-15', '2025-07-18', '2025-07-16', 'رنيم', 'معلق', 'معلق');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contract`
--
ALTER TABLE `contract`
  ADD PRIMARY KEY (`con_id`),
  ADD UNIQUE KEY `program_id` (`program_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`emp_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `guest`
--
ALTER TABLE `guest`
  ADD PRIMARY KEY (`guest_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`con_type`);

--
-- Indexes for table `vacation`
--
ALTER TABLE `vacation`
  ADD PRIMARY KEY (`vac_id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contract`
--
ALTER TABLE `contract`
  MODIFY `con_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vacation`
--
ALTER TABLE `vacation`
  MODIFY `vac_id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `vacation`
--
ALTER TABLE `vacation`
  ADD CONSTRAINT `vacation_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employee` (`emp_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

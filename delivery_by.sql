-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Дек 16 2025 г., 13:40
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `delivery_by`
--

-- --------------------------------------------------------

--
-- Структура таблицы `calculated_routes`
--

CREATE TABLE `calculated_routes` (
  `id` int(11) NOT NULL,
  `from_office_id` int(11) NOT NULL,
  `to_office_id` int(11) NOT NULL,
  `distance_km` decimal(8,2) NOT NULL,
  `duration_min` int(11) NOT NULL,
  `route_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `calculated_routes`
--

INSERT INTO `calculated_routes` (`id`, `from_office_id`, `to_office_id`, `distance_km`, `duration_min`, `route_data`, `created_at`) VALUES
(2, 826, 894, 160.68, 192, NULL, '2025-12-14 17:53:45'),
(3, 807, 894, 221.56, 265, NULL, '2025-12-14 18:19:08'),
(4, 960, 986, 237.22, 284, NULL, '2025-12-14 18:52:38'),
(5, 826, 828, 121.00, 145, NULL, '2025-12-15 07:15:34'),
(6, 975, 973, 248.01, 214, '\"uwa}Hugi~CMDYFw@Ns@NOD[JSFSFYNKDe@Xy@h@a@TWNWLUJoAb@YDUDeAP_BXaALw@JcBZgARs@LaATkAZ}@Pw@N[FaALOBi@JUD_APkCb@a@DE@oARkDj@]T}B`@_BVaBZgAPq@Ls@NsFv@KBs@HiAPc@Fe@Dm@JQBc@HeAPgAPq@L{@NkAR[Fo@JWFoDl@c@FQDeDj@]DUDa@F}@LQEIGIEWSEEEEECECE?EAE?E@GBEBEDEFCFEHAHGPITS^E?o@LWDyAV{Ex@uAVkARmAPwAV_ALWFa@HUBqCb@o@J_APu@L_BZaEt@_Dj@K@oCd@]Fg@Hq@NmBZwATmAPE?i@Js@L_GdAwAVqATmARc@FSDUDkAP_Fv@i@HWFa@DUD]DcANsC`@qAPgAN]FmANaALKBgANi@HmC\\\\YDOBi@Fk@Hi@FmAPI@[DuBX}Ft@{@L]DiANeH~@{ARSD]DOBk@HeD`@{APi@FOBWB]FkBRmHbAgBTqC^aALeBRWD}ATSB]DkDd@o@HWDeAN_ALcGt@[D_ANUB_Gx@i@FaAL}@Lw@Lo@Xc@Ve@`@sAfAuArAkBfB_LlKgBbBaBp@k@Pm@Lk@Bo@?m@K_@Kc@Sq@c@u@q@m@s@gC}CoAkBaAgBcAiB}@cCwAqF_AaFcCgM}BuLuAaH_BsGiC_JyCiIuDqIyA}C}@aBy@uAy@iAa@m@_@g@qDuE}@iAeAiAiGqGeMsLaF}EuEsEyIqImd@kc@gH}GQSMK}A{A}JoJ}E{EwNmNm@k@aA_Ao@o@k@g@oDkDq@q@_MuLaG}FgCiCsB_CeCcD_B_C{AcCsAcCmAaCoAqCaBsDcBiEqBiG_B_GkA{Ew@qDq@iDo@eD[yBaA}HeCwT_C_T_CyRsCiWcAkJQ{AyFyg@s@iG{@wHc@{Dg@cEMmAa@mD_CyScDsYcEe`@QaBoOitAkHqo@iFme@cAkJ{CqWyHgr@eAkJoDg[iBiPuDw\\\\eAiJaLecAcBeOGi@u@sGg@kEg@iEgC{TI}@MgACW_CkTsHkr@mFoe@gCgUyKubA{@{HkM}kA{Da^}AyNmAqKeAcJaB_OMgAkCeVgG}j@iBkQSkBmDi]{Gqp@[{CCOaBgPqKudAMuA_Eq`@i@iFSsBm@aG_A_JWiCq@qGiAsKaAgJwA_Ni@{Dq@}DeBcJ_BeH_AqDaA_DoBwFq@kB_@_A}AwDgAgCmA_CaDkFoDcGsRq\\\\kKsQkBaDwGaLgP}Xo@gAeD_GaAgBy@eBcByD}@eCg@oA_AsCkAwDmB}GiAgFsA_HgA}GQkAOcAUkB_@yCs@uHe@sH{AcUu@}MuBo\\\\_D_g@sAeTyAiUQuCiDoj@uAsRyAqRGcAQoBKoAoAuOsAsPcAoMyDye@k@gHqBsVOcBIgAsFur@OuBi@aHg@sGScCc@yFeGav@kCo]sCc^aCiZiFop@aEkg@{Ck`@oGyu@wKmqAmF}n@[gFMiCMiCOgEGgCGsCCoDAcC@mFFoIVmR\\\\}XVmSDaEPoMlAs`AFwDDsD?OHiF@iBLsIJcJT}QLsKPkNNsHBsABeAH}D`@{Kf@uJj@}Jd@yFxDub@`@iEnDa`@T}BhFsk@v@mIvE}g@rAgMtBqRPyApNipAp@{FxHor@jEi`@XaCxEqb@vH}q@n@yFNqAz@}HbFcd@xDi]dAkJtLmfAxBaSfJky@bAeJ|E_c@|H}r@rO_vAdEi_@h@sEfHqo@nJc{@dDiZvEcb@f@eFpCgVJcAfAwJf@iENuAbAeJRiB`Ew^p@iFpAiM|@_LZuEj@{LReGPkGXcLhAqb@XeKjBms@PaHN}Fj@yStCigA@k@b@iOJmErBew@n@oVZsMD_BRsLFoIBmC@kM?}@EwJM_LYkRGgDa@mVa@sXIqFK}GEcBAc@MuFIkDCg@WgHi@oJu@_NQwDw@qNqA}Uk@cKa@cHQgDm@}KAICi@SuDQeFG_EEkEE}DAaAC{AD}EBoBBmC@c@RuFTgFb@oHhCud@p@kLr@qLh@oIb@sFFw@r@oIbAkKnA_LrBoOzBeO|@mFbGe^bBaKdG}^rAiIhGs_@hAaHpAgHxBgL|DmSxEiVbNos@r@qD\\\\kBTeArAgHfPuz@dMwo@xB{KdEqT~BcMnAkGx@kEvEmV|DaSbB_J^mBhBuKnAyIdBsOl@uHf@}GF{@\\\\uGRsFXyKFuCHkCRaId@gWHcEhAwh@zByhAxAyu@fB_u@tC_vADiBJwEDuANoHDwAhAmj@FcCJcEDwBRoKViLPsH\\\\sNxA_p@PqHBeAjA}g@dAue@RcIFgCXoLVgLB{@Bm@TcJJsEFeBDkABk@ToFHsA^uF`@kFZmE~A_UdGoz@b@aGd@gHfDme@jB}WHcA\\\\_F|AwT`AoM~@gM|AiVLyDN{FD}A@o@@o@BaABeJ@yBEcFAkBKkFC_Bg@gOm@cP[uJKcC{@iU}@qWCg@K}CE}@QgFEaAUwGE_BUsFCaAE}@OkEyDwfAe@qMs@eSoDmbAI_CoEgnAWsHcCgs@{A{a@G{AC]}Bsp@}Awc@GeBASo@kQiCwt@k@gQKmCCyAcAsVsBam@e@}M{A{c@qBcj@UiH]aLImL?kC@yGBmG@iCBkBJiDPyFJyDLuGBg@XkKXgLv@k[RaIx@o[jAkd@Bw@zAml@N{F~@i_@tAkh@FiCD}ADeBHmDJ}DJiEJeEJyD`@cO@S@[DeBDsA`@cMHyD`@gRvD}yAn@iVFaCbBcn@NgFLiELoETkIDoBHgBBe@PmGRgI`Ag`@TaIViJjA}d@hB_t@`Bwn@t@uVToLFkCbDcoAB}CDgGRw[FsNLkQDuI@qCH{Ld@c}@BcCLcWAwIGcIEeFGgDKgGQwGQuE}Aya@g@{MoBei@_A{VWuHqAc\\\\[wIU{FCg@GaBEmAuAu_@IyB?MeBud@mCus@q@uPM_C_@yKmCuu@uAs^k@oO[_IA_@[sHY_Ia@sKS_Gs@yQCy@}Ckz@sFmzACk@SuFG}AIgCOuD}DeeAsEqmAYmIiCkv@uAw_@I}BoF}yAc@aMmEmkAqFmyA}DceA_@mT[}PkAmo@?Ue@}V}@eg@Y}O[aQYaOQ{LYwN_Bo{@y@if@C_BuAur@MeGUaNA]QqKGqCAgACsAIyDGkEC}@Q{NQ_K?]MeG_Bkv@Ay@EuB[qSM{GIyDM_LOkIS_Jm@_L}@gSIqAEcAOuCK}A_Ck`@OuCw@cQEs@iBg\\\\gAkRkGmeAy@uNqGaiAkBs\\\\uAwTM{BcA{P_A}OiCsb@KyAGkA_@{FoCef@C[GgAcCub@cBwYq@{KIaBQiCQ}Cw@iNgG}cAEy@_BoZkAoWC}@EwAOeFWuKS}LG_FDsf@?mf@Cw[G_eB?o\\\\@guA@{vAGcOcAsgBeAqpBa@}s@Wwe@CuC]mm@]}g@G_Mg@wy@aAi}AEeVA}@]{a@KyOCkPA{DA_eA?y@?iDXajDKojB?_Ch@q]DiCh@wShA{RTkDxHslAb@gGt@{JZ}DbGom@jg@ihE|NonApEs_@LaAXaCbNojAfAeJzKql@pSs~@Nu@vLsi@bYaqAtMam@vXopAdXilAbIa^lYcpAnGsZdFwZ~@uHfC_YpAiWJ_CLeETkIlBys@?a@tC{dAjCy`Ar@kWD{AT}IlBmr@jEktAfA}RxFkfAn@cMh@yJj@qKFgA~AkZv@uNLsB|FihAZoGdFcdAdCkd@jBy]T_El@yRJeHFwNeAi`A{Be|Ai@oYCiAGkDe@iWsDc~BEsCeAkp@AmAgD{sBGoDaCw|AAq@gAgs@Ak@GiDAo@cEwlCsAg{@?SE_C?UaCuzAkCicBQkMOqJEqCOeKyA{_Am@o`@CaAAgAQyKG{Ee@uYq@cc@S{Nq@ab@?e@yAc_ACgBuAo~@[uN_AyTIwBGaAUyDWwDoMgeBwCg`@Eg@gCc]IiAaCi[M_BeKuuA_CoZKiAIgAmGa|@}JirAEc@IoAMyAMkB]qEmPs{BQ}H?kH@eABsDL_IfAaSd@eIbCyOz@mFrBsMlD{TjHoc@vBoMtAqIf@_Dn@aEPcAvLou@zKyq@xAeJpEiYvBsMjBgLhMow@rDeUfOg_ANcAzTkuApK}p@bLsr@lN}{@lGm`@nHkd@xAaJ~CyRbOu~@~Fm^v@aF`EyVf@aDzFe^jSsoA|H}e@xC_R`DeSZgBlEsWbAcHzAoJjFm\\\\P_AlKep@|Jan@jKyo@bDqSnFc]lVc{A|CoR`Jwj@nOi`AnGe`@rHud@fC{OnEiVdAmE~CeMnF_RlEeNL_@Vu@`AeCb@mA~F{PlRuh@tHaQtH_N|BiDx@mAdGyHjEiEdHeG|MmLZWxAkAl@e@j@e@`LqJfA_AxFwEbIyG~P{NxE_EbI_HxGuFnIaHfDoC`@]zG{FrC_CzB_CtDeEdE{FlByCr@sATc@f@}@nBgEnBuE|CeJfAgDrBuHfAgF~@iHn@wIFcFBgBIkGFu@He@Pg@fD}@|C}@nCw@|XiIr@Wx@c@n@]v\\\\yRvNeIvFcDxFaDpBoA|BqAjC{AbC{AdCwAlC}AvG{DxIcFfBcAfBcAtEgClAs@pAq@tAq@fBcALCNCHAHAJ@LPPTXVNFTBJCPGH?H?HDLJXN~ArBfh@bp@~ArBtQrUf@t@rGrJxGzJ|CtElAfBd@t@fEjGr@dA`CrDd@p@dA~AHNb@p@|BlD`@n@T\\\\Zb@Tb@|@vApBxCnDpFzBrDpBjD~IhRbB~Cp@dA|@lAp@n@lB~Api@~PpBd@xCt@f@JrBd@hYtGtCp@~J|Bn@NNFbAZrAf@Xd@F\\\\@fAF`@Yl@eAtBEFMAQAQ@][QQQOMWIYG_@?e@Dg@ToCJ_BRmANmA^gBl@iBlAiDlBoExAgD`BiDf@}@f@aANGLM\"', '2025-12-16 09:35:51'),
(7, 975, 974, 172.90, 154, '\"uwa}Hugi~CMDYFw@Ns@NOD[JSFSFYNKDe@Xy@h@a@TWNWLUJoAb@YDUDeAP_BXaALw@JcBZgARs@LaATkAZ}@Pw@N[FaALOBi@JUD_APkCb@a@DE@oARkDj@]T}B`@_BVaBZgAPq@Ls@NsFv@KBs@HiAPc@Fe@Dm@JQBc@HeAPgAPq@L{@NkAR[Fo@JWFoDl@c@FQDeDj@]DUDa@F}@LQEIGIEWSEEEEECECE?EAE?E@GBEBEDEFCFEHAHGPITS^E?o@LWDyAV{Ex@uAVkARmAPwAV_ALWFa@HUBqCb@o@J_APu@L_BZaEt@_Dj@K@oCd@]Fg@Hq@NmBZwATmAPE?i@Js@L_GdAwAVqATmARc@FSDUDkAP_Fv@i@HWFa@DUD]DcANsC`@qAPgAN]FmANaALKBgANi@HmC\\\\YDOBi@Fk@Hi@FmAPI@[DuBX}Ft@{@L]DiANeH~@{ARSD]DOBk@HeD`@{APi@FOBWB]FkBRmHbAgBTqC^aALeBRWD}ATSB]DkDd@o@HWDeAN_ALcGt@[D_ANUB_Gx@i@FaAL}@Lw@Lo@Xc@Ve@`@sAfAuArAkBfB_LlKgBbBaBp@k@Pm@Lk@Bo@?m@K_@Kc@Sq@c@u@q@m@s@gC}CoAkBaAgBcAiB}@cCwAqF_AaFcCgM}BuLuAaH_BsGiC_JyCiIuDqIyA}C}@aBy@uAy@iAa@m@_@g@qDuE}@iAeAiAiGqGeMsLaF}EuEsEyIqImd@kc@gH}GQSMK}A{A}JoJ}E{EwNmNm@k@aA_Ao@o@k@g@oDkDq@q@_MuLaG}FgCiCsB_CeCcD_B_C{AcCsAcCmAaCoAqCaBsDcBiEqBiG_B_GkA{Ew@qDq@iDo@eD[yBaA}HeCwT_C_T_CyRsCiWcAkJQ{AyFyg@s@iG{@wHc@{Dg@cEMmAa@mD_CyScDsYcEe`@QaBoOitAkHqo@iFme@cAkJ{CqWyHgr@eAkJoDg[iBiPuDw\\\\eAiJaLecAcBeOGi@u@sGg@kEg@iEgC{TI}@MgACW_CkTsHkr@mFoe@gCgUyKubA{@{HkM}kA{Da^}AyNmAqKeAcJaB_OMgAkCeVgG}j@iBkQSkBmDi]{Gqp@[{CCOaBgPqKudAMuA_Eq`@i@iFSsBm@aG_A_JWiCq@qGiAsKaAgJwA_Ni@{Dq@}DeBcJ_BeH_AqDaA_DoBwFq@kB_@_A}AwDgAgCmA_CaDkFoDcGsRq\\\\kKsQkBaDwGaLgP}Xo@gAeD_GaAgBy@eBcByD}@eCg@oA_AsCkAwDmB}GiAgFsA_HgA}GQkAOcAUkB_@yCs@uHe@sH{AcUu@}MuBo\\\\_D_g@sAeTyAiUQuCiDoj@uAsRyAqRGcAQoBKoAoAuOsAsPcAoMyDye@k@gHqBsVOcBIgAsFur@OuBi@aHg@sGScCc@yFeGav@kCo]sCc^aCiZiFop@aEkg@{Ck`@oGyu@wKmqAmF}n@[gFMiCMiCOgEGgCGsCCoDAcC@mFFoIVmR\\\\}XVmSDaEPoMlAs`AFwDDsD?OHiF@iBLsIJcJT}QLsKPkNNsHBsABeAH}D`@{Kf@uJj@}Jd@yFxDub@`@iEnDa`@T}BhFsk@v@mIvE}g@rAgMtBqRPyApNipAp@{FxHor@jEi`@XaCxEqb@vH}q@n@yFNqAz@}HbFcd@xDi]dAkJtLmfAxBaSfJky@bAeJ|E_c@|H}r@rO_vAdEi_@h@sEfHqo@nJc{@dDiZvEcb@f@eFpCgVJcAfAwJf@iENuAbAeJRiB`Ew^p@iFpAiM|@_LZuEj@{LReGPkGXcLhAqb@XeKjBms@PaHN}Fj@yStCigA@k@b@iOJmErBew@n@oVZsMD_BRsLFoIBmC@kM?}@EwJM_LYkRGgDa@mVa@sXIqFK}GEcBAc@MuFIkDCg@WgHi@oJu@_NQwDw@qNqA}Uk@cKa@cHQgDm@}KAICi@SuDQeFG_EEkEE}DAaAC{AD}EBoBBmC@c@RuFTgFb@oHhCud@p@kLr@qLh@oIb@sFFw@r@oIbAkKnA_LrBoOzBeO|@mFbGe^bBaKdG}^rAiIhGs_@hAaHpAgHxBgL|DmSxEiVbNos@r@qD\\\\kBTeArAgHfPuz@dMwo@xB{KdEqT~BcMnAkGx@kEvEmV|DaSbB_J^mBhBuKnAyIdBsOl@uHf@}GF{@\\\\uGRsFXyKFuCHkCRaId@gWHcEhAwh@zByhAxAyu@fB_u@tC_vADiBJwEDuANoHDwAhAmj@FcCJcEDwBRoKViLPsH\\\\sNxA_p@PqHBeAjA}g@dAue@RcIFgCXoLVgLB{@Bm@TcJJsEFeBDkABk@ToFHsA^uF`@kFZmE~A_UdGoz@b@aGd@gHfDme@jB}WHcA\\\\_F|AwT`AoM~@gM|AiVLyDN{FD}A@o@@o@BaABeJ@yBEcFAkBKkFC_Bg@gOm@cP[uJKcC{@iU}@qWCg@K}CE}@QgFEaAUwGE_BUsFCaAE}@OkEyDwfAe@qMs@eSoDmbAI_CoEgnAWsHcCgs@{A{a@G{AC]}Bsp@}Awc@GeBASo@kQiCwt@k@gQKmCCyAcAsVsBam@e@}M{A{c@qBcj@UiH]aLImL?kC@yGBmG@iCBkBJiDPyFJyDLuGBg@XkKXgLv@k[RaIx@o[jAkd@Bw@zAml@N{F~@i_@tAkh@FiCD}ADeBHmDJ}DJiEJeEJyD`@cO@S@[DeBDsA`@cMhAHBRDF`ADB{@SAMAc@CGNAPiAIa@bMErAEdBAZARa@bOKxDKdEKhEUfA]|@]f@a@`@e@Pm@NyAOm@Ma@WQQm@q@Sa@I]EWGg@yAp@yE|BuDdB}Ah@eBf@}Cz@m@L_B\\\\{C^aBJeBJeBDgIDyAGuJQmACiFIoBAwP[a\\\\c@eIQiGMo@AmQ[m@CsEImRa@gLU}@A_Pc@wCGwEa@iKqAkDc@yNeBeAOcLsAqDc@mM{Ay]oEs@K{Gy@mIeAoBUsNiB{@Iae@}FeQwBaKqAuHy@mOmBmf@kG}KuA_BSuBWsJkAiVyCyOqBiBUqIcAiH{@aGu@aD]kFm@k@IQCiDc@iC[SCSCaFq@iAMgIkAib@kFs^qEmQyBgEg@kKqA}@Mwf@iGoBW}HaAoG{@ib@eFkEk@cPoBeAMsBWsXkDyt@eJqAOuG{@iGm@mIg@}Ik@aAGiH_@eEUev@eE{F[gTkAqUsA}CQc\\\\kBaZuAi_@qBwVyAyQgAyc@cCkEQo\\\\iBiSkAWAg[}Ag@EeEWeBI_P_AqMq@iX{AkH]c@Cm@AkB?_GBw@?}m@`Ag^d@gX`@on@x@_GDmLNcKNeJNiR\\\\oX\\\\}BB}Yd@eOJuGLqOZwHHaFFoWd@{DDiDD_@@cFFyFFaA?gABoFHcCDcDFgFHWAq@@oFJ_RRwCFac@l@qOTiFHwh@v@iB@yCD{HHcJN_GJy@@gBBeDFO?uOXiGF}ABoBAeQ\\\\iQFwC@iD?mDQuEOeFSuDMqFUsFScHUsCKys@_CSAY?uCCoCFoH\\\\oD^qDj@yw@`MqTbDe@H_RpCq\\\\hFyFx@cBVyDj@mC^m@HoOlCaWzDep@~J{ItAiAPcG~@]Dqe@lHoNxBiEp@gb@tG_O|BsCb@mCb@gIpA]F}HlAgG~@{HjAaInAkBZy@LeFx@sFz@cNvBG@aHdAcBX{QpC_UjDoJzAib@tG_D`@kEv@oDh@_ALsUvDaFt@K@qRzCgDh@qATu@J}MtBqARiBVmDl@aANsBZaBVaANaAJ{@D_ABw@BmB@mAAq@AoDAWAu@?gDCoDAq@AqAAoAAwBA_BAM?}DU}B_@kASkBe@wEuA}CiAs@WgD{AcCsAgBeAe@WsCmB}ByAmByA]WwE_EmEmE}QyPkW_Vu@s@eFuEmAiAUQo@m@TiAHmAEkP?s@Au@@cB@y@@]Do@Be@|Ae\\\\ZsFTsDDa@D]BYN_AJm@Po@Li@Tq@J]N_@N_@P_@P]R[Zc@j@s@rA}Ae@e@a@[eOoLs@q@aBuAoEoDsHcG{BgByBkB}DyCsB_B}@q@cEiDgBwAoHwFw@o@QO_CkBwCaCSO_CkBGEaCkBk@c@}@u@IEi@c@{FmE{OgMuFiEkHuFeAy@gWwSiAaA_BkAoH{FoCmBeAe@aAUaHAyAAe@?AqA@yC@{D?oE@oG@{E?cBEeAGcAQyAIsAg@kCKi@SeAKc@Uw@[gAKa@Mq@WiAGW]gBa@oBc@qBg@}BI_@UgAMm@CKc@uB]eBMm@CII]UiA_@gBQy@GYm@qC[{AEWg@}Bc@uBe@}BYwAc@mBMi@AEYoAm@qCOu@Qw@mAuFa@kBq@gDUeA[yAKg@]aBYuA}@gES_AoBgJGUIc@WmAMg@Mm@_@gBWkAk@sCe@_CEY\"', '2025-12-16 09:37:16'),
(8, 975, 894, 183.19, 178, '\"uwa}Hugi~CMDYFw@Ns@NOD[JSFSFYNKDe@Xy@h@a@TWNWLUJoAb@YDUDeAP_BXaALw@JcBZgARs@LaATkAZ}@Pw@N[FaALOBi@JUD_APkCb@a@DE@oARkDj@]T}B`@_BVaBZgAPq@Ls@NsFv@KBs@HiAPc@Fe@Dm@JQBc@HeAPgAPq@L{@NkAR[Fo@JWFoDl@c@FQDeDj@]DUDa@F}@LQEIGIEWSEEEEECECE?EAE?E@GBEBEDEFCFEHAHGPITS^E?o@LWDyAV{Ex@uAVkARmAPwAV_ALWFa@HUBqCb@o@J_APu@L_BZaEt@_Dj@K@oCd@]Fg@Hq@NmBZwATmAPE?i@Js@L_GdAwAVqATmARc@FSDUDkAP_Fv@i@HWFa@DUD]DcANsC`@qAPgAN]FmANaALKBgANi@HmC\\\\YDOBi@Fk@Hi@FmAPI@[DuBX}Ft@{@L]DiANeH~@{ARSD]DOBk@HeD`@{APi@FOBWB]FkBRmHbAgBTqC^aALeBRWD}ATSB]DkDd@o@HWDeAN_ALcGt@[D_ANUB_Gx@i@FaAL}@Lw@Lo@Xc@Ve@`@sAfAuArAkBfB_LlKgBbBmAhAaA`AuAnAmG`GaB~AsBpBcItHeC~BkCdC_DrCmCjCcC|BIHoNxM_@\\\\{AnA_A`AsArAOLiAfA}@x@{AxAuBtBo@r@]^WVGH_@f@o@v@_AbAIJcBlBc@h@UZy@nA_AzAcF|Ic@x@yBvDmBbDuQb[wJpP}AlCsTz_@aDtFuKxQeCpEwWtc@{@xA{HvMILu@pAyKdRs@lAkAnBmGlKWf@GHaG`Kw@rAeDvFcDtFYf@KPORk@`AS\\\\a@n@yAfC_DjFqGxKS\\\\OVoDfGaA`BADqKpQ[h@m@dAsA|Bo@dAU`@e@v@EFyDzGcDrFGJwAbCsAfCyAzC{C`HeB`EoDdKiDnK{FhQ{ApEuBvGqBdGsHbT_AnCa@fA_@t@m@`AKNEFOTMPQRa@f@o@h@q@h@m@\\\\q@HmAZaALkADsACqAUkAQyIgCqA[mC_AcBy@s@g@g@]yE}CgAu@yDgCkDgCsEqCsBeAkBw@sAe@kAYoCa@uAQkDe@uD_@_COuA?qA?cEDeENuFJsFJsP`@aBD{CFyINmKT_MHsYl@a@@iCJg^p@yFReGL_EH_IPic@~@m@B_IPkKZwAB{HJcJZkDFgGL_@@_@BM?c@@aVj@wINqKZM@aDFqAB[@{BFc@@{BFuADG?kCH}EJiBDmCFoADM?yAByABy@@[@q@@q@@mBFsEL}IToLVgCFmCFwADgA?mABW@i@Da@De@JUFcAX_Bh@cGzBKDSHi@R{DxAKDGBSHc@Py@XsBt@WJ_Bh@yBz@qAd@uEhBcDrAsB~@OHk@^a@\\\\]Z[Zm@n@{BrCo@v@aDbEgB~BwFjHcSxVaFjG{AlBeDlEiAxAgDlEoA~AwDzEqKrN}GzIoHfJwHbKgAvAsRhWs@z@k@v@}C|DMNuCtDUX}AtB_EnFgEnFmE~FqK|MeC|CqZr`@m@r@aEhFkEvFUZaDdEiAvAyAlBe@l@q@z@SX]b@iE~FkExGmFjI{A`CwAzBiAfByAhCgAvBiDjHSd@mG`NwDbImDvH}CdH{@vBiBjEeAtCw@dCc@jBa@|BuAxKeCpUg@nE}BpSmDb\\\\}Dn^UtCCJMdBMxAc@rG_@jJKxBu@vQg@bNaC|l@SfEOnBUvBGb@m@xEc@lCg@lCUfAUhAaAbD{AbEm@vAi@hAm@hAm@dAmAlBqAlBsBlCgF`HaC`D}Xf_@q@|@o@z@qAbBq@z@k@x@mE`GwQlVgFfHeFvGiD~D}A|AcB~AwAlAmA~@eAx@wAbA{BpAcCpAkB`AuBt@{Bn@yD|@iBXcBRs@DwJr@qGb@yDVkW|AmQhAsM`AaAFiJl@eDPoAJsPlAoJl@mXnBmAFmZrBwDVyD\\\\cIh@qEX{BLsBDuB@oBE}@C{@Km@K}Do@}@QkAUeAS{Dy@_@GiDs@wDu@g@Ki@MkBa@mIaBaEw@}Ci@iAQsAMsAKo@E_]iBkUkAsIc@{Kk@mOw@{EUsDO}@AS?{@@cANeCn@mAd@}At@oAx@mAdA_@b@s@v@yAtBkGnJgExGeEnGcKxOeElGkE|G{I|M{FxIsLvQmGtJ_Td\\\\_D|EuEfHqDvFkAdBm@xA_@nAOl@Oz@a@zF{AjUs@dKaAbOYzDiCx]mCl^M|Am@nIcC|\\\\i@pH_@rFcCp^_Cl]aGf|@kG|}@Q`EEt@Y~JS|Fs@lSStGm@dS_@fKYdF[pEk@~Gk@rFSfByAnNUzBk@nFSbB]|CoAbHMlAMtAMpBmArQ_@jFUlDg@lHoC~a@kCv`@IlAa@|HEtACdAc@lYMxIWxOIhHEdCAv@CrCCzACdBAfAAp@Ab@?\\\\IzEChBC~@Ix@UdAe@pBOh@[pAQt@Wt@i@xA}AvDyDhJaA~By@tBSb@g@hAO\\\\oAvCc@`Ay@lBYn@qA~CyEjLeAbCuFrMiCfGgBdEsK|VqAxC{EbLMXQ`@iCbG}BlF}@vB{DhJgAjCIR]v@i@lAiAlCo@|Ai@pAM\\\\mAtCaAzBsCbHsCbHuAlDw@zBwAbDeCzGwGjP]z@iAvCcFdMaCbGaBdEkAvCcOn_@uGnPiUbk@Ob@yG`RqGpSy@fCi@bBaBxEgF|LaBpDuJ`TwBrE}CvG_C~E_@t@qAvCo@fBe@jBc@pBeBjIq@hDMj@Mj@uA`GSbA[nAEV_AdEGV{@xDsAhFs@`Cq@dB_AtBiAlBkAdBkAvAwApAgIhHgDlCi@b@cN|K_GrEgGtEcDdCqF~DsB|AeJ|GmBxAcBxAw@t@a@f@s@z@cAvA}@pAeAhBqAnCiAlCqCzGuAdD[v@aAvBq@rA_AxAs@`Ay@dAaAhAcAbAmAdAUPg@\\\\_Ah@cAb@s@ZOFSF{@RsBTaADeBJeL^qGTcK`@wALeAP_AR_Bh@eAd@_Ab@s@\\\\[Py@f@k@\\\\m@`@q@f@i@f@i@h@oBzBqA~AQV]`@{F~HeBhCgCzDm@~@MZUh@a@jA_@jA_@~ASbASbAKdAMxAWdEEz@Ep@CpACjA@jADvC@z@H~E@f@F`DPnJBbBBtCB~CApA?pACn@Et@Gj@g@hES`By@~FgDnWo@vE{Ffc@yIzo@g@tDs@pEe@zBQr@_AzDsBnIaO~m@{@nDmAjEg@dBuArEw@~By@~B_AbC_AxB]x@[p@sC`Ge@dAg@~@g@~@]n@MTq@bAm@z@c@n@yAhBo@v@q@v@uC|C{@z@{FnFmBdBcGtFuEhEuApA}MbMoAhA}@z@iCjCkAxAiAzAcElGwDxFs@jAg@~@q@tAe@bAkArD}@`Cq@lBm@|A}@`CGX_AzDyBvI]zAqRr{@mExRcSj}@yDbQa@hB_@~AUdAUlAKr@[dDQxBQzBU|DM`EEzC?rB?zABtAJzGPdG?d@BbB@jCEdCGxBQfCW~DIlAGhAKvAk@vJYhE{Bb^UfDOxBwA~U}@vMs@fLwCbc@OzBAPsAdS_@tFaBzUuA|SKvAq@nJWdD[dDk@pDk@dCo@~BeAjD{CtJkIdXoInXuAxEyHxV_@jAqApDaAlBiA|BoA|BiA|AaBnB}VtYuK|LcA|@qAv@g@TiCjAe@P_@Le@NyKlDyFhBcHzBkBl@eBj@wBt@yItCgA^kInCeEvAsHdCyAh@}@^oKrE}Ah@oCjAc@NOFyBz@mCfAeChAyF~B}@b@_Av@c@f@a@d@_@b@KLk@l@cAhAY\\\\KJCFg@f@{@`AeBlBkBrBIH]^}A`Bs@v@u@v@gCrCQTOLw@~@}@dAeAfAy@|@GFuA|AKJGFeAhAy@`AWXq@r@aBbBo@r@a@d@k@n@EDq@t@QRi@n@[\\\\oArAKLSTy@|@UXg@h@g@f@wAbBSVYVc@b@{@~@g@l@w@fA[t@Qr@Kf@E\\\\ETM|Ag@xFw@dJE`@o@vGKrAIt@Ix@APCTStAUnAY|@Qb@QXcAfA]\\\\cA`AeA`AURwBjBuAbAcBjAc@\\\\GDYTe@^a@Zc@\\\\a@Zo@f@{B|AOL[LtF|VRx@fB|HfBzHFXJh@WJw@Zo@^a@\\\\]\\\\Y`@[d@y@vA[n@mAdCsDpHy@`BKTQZuBvEqAjD}@|Co@jCm@fDc@nCYrCy@|JEh@_Dhb@KxAu@|JuIviAuDnf@_Fnp@OfB]lEw@rKm@|Hk@pH}BzZyBvYCh@Gv@k@`IOnB}ApSq@vIUtCOpB]rEy@nL}@`MqApQgF~r@_@dFkCx^}@~LsF~q@eCpZIdAw@pKqDhf@_@dF]vEiKnuAs@tIu@hJ_BfRmB~Vk@|Fm@nEo@nDi@dCWhAi@hBe@zAwCzIeCtHY~@]dAg@|B]vBQdBMfAOxCCfC@~Ej@dj@l@nh@B|ADhD@zC@r@@nA@fA@`ACbCC|@GdAK~AIr@S~A]fC[rAQl@o@lBu@bByAvCaB`DwB|DOXoHrNuB`E}BlEkEdIoBxD{Qn^wGtMu@zAcBnDeBbEy@bCc@bBQz@WvAQjAOlAEdACbACjAAlBDfBH~BNnEd@jMz@tWXxHJdCBz@FbBBdAhApZXjH`Czo@d@tMrAf`@^~JfAb[b@bMNtDLvD`Bbc@dArYLbDrAj_@|@jXFhBHfBlAr\\\\v@pTtAl_@H~Av@fJnBtTbA~K|@rJZdCvArINt@J~@Fr@B~@AjBu@|IiAbM_AfKoApNc@lFM|@QbAUn@EJINKPMV]h@aA`AyEdEaCtBe@d@_FfEa@\\\\oGtFcX|U}DjDQNQPaJ|HwDlDyVlTyCpCiGzFqJ~IgRdQiXlWk@j@aOjNu^t]cOjNaJtIqCrCuCvCeCpCuFrG}JbLoCzCwBbC_HbImCxCsEjEiAdAsAnAs@n@wSnRw^d[q@h@wBjB}DhDcJxH}@t@o@f@q@f@uBvAaFxCuItFoAv@}JnGaBlAu@p@eAfAuZp\\\\sG`HsAzAc@`@_GfGgUzVgAjAMNmAnAg@l@k@n@o@p@WXoAvA[d@Ux@Y|Ag@vCSl@QZe@t@g@x@aDvEs@bAqFnIiAhBu@fB{BbGoAhDqExLeIjTgFvN]`AuAfDi@pBu@fCM^c@~AWnAIp@K|@_@xCI`@KZaBtCcAhBQ\\\\gBhD_@v@]v@aF~NwA~D_ItUeBfFmBjFQl@mD`KENCFo@jB[`AGPeCjHyAlEc@~AYhAMp@[pBCJELCHEJGH_@j@OTMTMRKTITIVIXGRGVo@pCIXAHe@pBGRaAhEi@bCaAjEEVcAtEGRMh@EEGEICGAIAG?K?I?yCLG?uBH]@uABqAF]@_@@uAHK?cCJ}@D}@BS?o@Bi@CYE[Cs@QWECACX?\\\\Bj@FlANjCBXH`BDjABjA@nB@vA?tAAbGCrBEhAEfAMdBObBM|AIlAC`AC`CCdC@xB?n@BzA?bAB~AB|BFhE?j@@l@BdC?bBChAIvAo@CeAGiAOSAg@IwEg@MAwCUiHs@eBQ}AEiAFcANaAPs@Tu@\\\\sAr@gAv@_DpC_GhF}FhF_BrAw@r@YZUVw@lAw@vAeBxDiAdD_@`BWnA_@`CWxBuB~WcAlN}C~a@u@fKkBzWANw@rKiBzWkBtYc@dHIjAYtEqD|k@]rD[~B{@`E_@pA]jAk@bBIRSb@MZcB`D_GdK_GlKeDdGq@xAq@jBm@pBg@`Cc@nCUlBQxAOrCUtEgAdWaBj`@QxDUlDKfAMhAc@hCk@dCk@xBa@rAwIlY_AbCu@bB_AxAcDfEqE|FgGbIc@j@iAzAcApAa@f@oA~AyD|DsBdByEjDkAz@s@p@g@f@]d@kAzBo@nAsAfCiC`FcArBc@~@i@pAw@zBu@fCG^]nB}ApJwFz^_CjOeA~Gy@fFeRzmAsHlf@qIfj@SrAkBnLStAo@bE]zBm@bEgAfHmAdIERCNCRYfBQbAAFCPCRI`@YlBETCRO`AO`AUzAKn@{AzJaAnGeClPyE~Z_AjG}CfSMv@Kp@a@dCq@rEuAdJ}AhKkAfI}@~Fm@xDaBdKi@rCg@pBy@jC]r@{@lBs@hAQXmBjCaCrCo@t@oH|IgOtQqEpFqA~AcBrBoChDyAjB{AfCsC~EcPjZeCtEiLfTmFzJ]p@uGzLaI~N[l@_f@n}@eCtE}CzFc@x@wO~Ym@jAcKfRsEpIiHtM{InPaL|SySj`@w@vAi@`A_EtHmDvGMVuClFu@vAm@dA}BdEmA~B{BjEg@x@k@t@qAbAQN]XkB~@o@ZiAf@SJsAn@iAh@}@l@a@^g@j@]\\\\]NqAj@kAf@yB~@KDgDvAOF]Nu@XWHaBv@oBx@_HtCmBz@uD|Aa@P{IvDkCfAmAf@w]|NyAn@gSnIsJhEyAp@}OxGWJ_C~@mAf@g@TqGjCa@Ho@AwAGC?}CM_I[kg@uBC?e_@_BaBIoBIsDQy@CyHYeNi@mMg@sV_AqBGSAq@?y@HgAVaA^{A|@}CtB{B|Ac[|SwA`A{OtKmD`CyDhCuNvJ}F|DkNjJgBlAwGpE{CtBQJiCbBqBrA{BxAmAx@}B~ASLeT|NmTrNeDvBaCdBQLyOlKsAfAsAjAoBjBeAjA}FtGsA|AqDbEoBtBMNmCxCKJwC`Dm@r@qJnKyB~BeCzBiCdB{CbB}Bz@u@TmA\\\\oK`CcMlCcBd@}Ad@_CfAsAv@gBrAw@j@w@r@u@p@y@|@{AjBw@hAuKnOuAlByBlDkBnD}AlDo@`B_ArCqArEe@lBa@nB{AhI[~AQfAaAtEa@`Ba@vA{@hCwA`Du@vA{@vAy@nAgAtA{GtIiHdJkBdCwBrC_G|HaJlMiJvM{H~KwBzCcBzBiAjAoAfA_DlByBpAoBhAkJtFqGxDmAz@kBdBWTeG|FqGbG_^p\\\\eVdUaXpVYXua@~_@iLtK{E~E}@v@KJa@?[E_@OQa@OUYWOEQCQDOHMJINMn@Ip@Az@Dx@RlAX`AT`@RV\\\\p@Tt@Rv@fCpg@`@|IzBld@x@tPt@tODt@p@dLF~APrDBr@DjB?hBA~Aw@hYc@zMOxFQ~EMxCMfAQ~@o@jCeBhGs@`Ba@l@a@f@kAdAsA~@_BhAsJhHkAz@WPSNu@h@]Tc@Z{@n@oAz@aAp@[TKFiBnAg@\\\\}BdBg@f@QRSZW`@{@^G@KD_@NYHm@PwBh@{EhAi@RQLq@j@s@x@w@pAqA~AEDcAhAy@n@m@b@m@b@c@`@]ZBj@D~@\\\\~H?NB\\\\PTj@v@z@vA^v@DIj@kA\"', '2025-12-16 09:37:53'),
(9, 926, 940, 393.88, 292, '\"oaacIqtpyCVGp@GJCLAKkFC{D?Y@gA?W?a@AUa@eH?Ge@uHe@kGCa@CWE[Ci@EaAAq@Ae@A]Ac@AWCcC@q@@e@B_BFsADaBD{@FyBFyADyAD_A@e@F}CBo@LsFDwAL}FBi@@YHyC@o@DeB@_@Fc@BSJe@J_@No@No@FUFSTeAf@_Cl@cCt@{Cx@iDBI\\\\qBGICKKu@I{@OyAIs@CGGUMa@KQCEGIEGEGQSGMKWm@yBOw@Gs@Co@?gAFmC@w@Bg@BgBBo@JeEBiADeBJ_HRwJDkB@SH}C@i@H}CX}HBgC@oB?q@As@a@iL]aIIuBEwAEiAEiAUwFEuAQwFOuDEaBQoEAYIuBE}AGuACg@Ae@yAyb@m@uQ[{JKwCAg@A_@k@ePwAmb@Cq@o@}PoAa]q@mUM}D]gQY{TImGGmK@qDJcDv@_Pz@eQrAuYrCmm@zBuf@PqDXwDj@}EjAkIz@qFxGwa@nDwT|A{JZmBdAwGvCyQN_AJs@TsA`DmTv@oFf@eEj@aEf@cEh@uELaBP{BJeC@_B?m@?gBC}D?e@AgCAYEaGEuFA{@OwDUoFm@kMYyC_Fw^kC{RoAwI}AuKeCcRgAgLeBsQa@gEcA{Mk@wLs@sQ}@uPaAaLi@cGwAeP_BsOyAaO_AqM}@aOc@iHi@cJe@qImA_Tu@uJeAmK]{BSmA{@iFiBaJqHe]eNwn@Oq@kD_PkAoFy@yDkAmI{@aJa@sEmAiUa@gJKeCoBic@wAmZcAeSi@aLOkAq@eGGSiC}LG]{@aEyB_LsZiiBiFs[g@sC{Kkp@oIw`@Mo@_Ia^iAiFmJqh@a@iBMm@a[wgA{@aDyL_d@aOqh@{Mkd@oAkEsHoWq@_CuC{JwCqK_Roo@{BcIsAaFi@kBg@{AGQgAkDaBaFaAqCcFeOi@oBe@gBWaAWiAk@yCGk@Gi@Eu@Cs@Ae@?g@@kBBcC@UD}B?SDsABq@Di@Hs@Js@RiAN{@VoBTaBBe@Bi@?Q?_A@qEBsJBsCBmNBoH@}DBeF@cLJa\\\\@sAHiYB}D@kEXybA@q@@wJB}G@yA@a@HsCNcEBa@JyCRgI\\\\cVTgVD{CDqCB{Cd@_c@f@oi@DgE^uXP{NN}MPoMLkKDoHZg[HoFPaOBaAB{BFwEHaGD{CLqN`@m[PoNNoMd@u]DyBNmLv@qn@BgCXuYZaUR_PFyEJmDViFt@iLpAcSFgADq@JaBd@oHHiAJkBHmA`@{Gd@kIFcAj@uKDq@d@eLX_IHcEDeEBeB?YBwBBiB?g@?eA?_A?u@?a@A{@OiLAi@Q_IAo@EqBSyJIkCAe@G_DGwCEsAAi@e@wVKwECcAEwA?gABgDFwCJqEFwDHcFJ_CNkFFeBD}ALuD@WDaBXkGJ_DZmHBs@Bw@Ba@NqDFcC^DfC^f@Hr@FL?PGJWJm@LiAJq@|@iDjCkJjAqElA}D^gAn@}ARo@Fa@\\\\e@l@wAr@kBRm@P{@J}@@q@Ae@Ca@Is@E_@Sq@e@gAoBqEsB_GyAgFuAsFsCqMg@wAe@{@a@c@[WWUSQ_G_EIMGKGWI[Ac@?g@BOFYHOHKLEpCSjAIrCY|Ho@B\\\\HpAo@z@i@j@_Ad@iB\\\\q@CMIOQWi@yAcIm@iDcCeNgBuJwA}HiHea@i@sCwGk_@w@mEuEwWoA{GiAkGyBeL}@yD_A{D{BuIk@iBoA{D_CmHqDcJyC}GsDoHaDcGqCmEeCuDgA}AeAyA_C}CkAqA{CkDsDuD{GqGsGoGgEgE{A}AkAoAgBoBmB_C_AoAeC_D}A{B{A}BcCcE_ByCiCgFaAuBuAmD_BwD}AeEM[cAwCsBgG{B{GsGaSiEuMwEeNu@aCu@}BqCsJaCeJyB}IqB{IOq@{BcLeCmMc@wB_@gB_@qBc@}BsAaHsAcIkBmLw@aGq@yFi@_Fy@}Jc@gGGs@]}G]_JKsDQkICeBY}UQ}JCqAKkEScG[wG_@gGc@mGc@cFu@sHq@aGeA{H_AaGcAcGaBeIYmAaBuHmB{GMa@aBwFyAmFwAgEsAuDqAiDgCeGw@aBoDqHsCyF_CkFyCwG_DwGw@cBoCgGqByEeCcGwAqDgBmFkIoU_AkCuAsDcRki@mCqHoBwFe@qAkI_VeIaUeCkHYu@kCgI_AeDmByGsAeFMi@gByH}AsHaAkF]iB]sBqAaIa@kCmBsLgBgLeJol@cBiKaD}PgC{KuEqRk@qBkCyJgCsI}A}EeAwDwDqK_CgG_CuFiCcGiDmHmEwIuB_EuAkCgIoOiIuO}IuPoEmIwBoEiDmHsDmIsEsLgB_F}BwGuBaH_BgFaAaD_CyIuCcLgJ_`@oEwQaOum@kFmT}BqJi[{pAkA_FgH{YcCgLeC{LaCqLeAwFwCsO}BqN_CkNiBwLwBoPw@}F_A{GuBsOqCoSaBuLiEy[qAwJuD_YU}AiAeIwAuKiDgW_AaHsCaTgBsM}AgLs@aFeAuGqAmH]mBc@}BwAmH{CiNgDeN{BoIcCiIoDcLs@uBm@eB_EyKiM}[i@sAyBwF{@uB}BgGkAyCuDqJmNc^yI}TwBuFgGyOyFgOgCgH}CmJ_DwKy@{CcFyR{CmNqCuNoIac@o@aDsHw_@w@}DmBsJ_@yBgF{W_C{LoAuFcAsEiAsEeC{J_AaDqA{EyBiHaCoHw@yByCqIuBuFqByEwD_JiEaJ{CeGgDiGwC_Fk@_AuIoNuGsKkLeRqEuHuC_F[g@}BsD}GaLaIkM}HqMyE}HiAmBuGuKwHkMyM}V}EwJ{ByEwMcYwM{XqEmJgHeOq@uAiEkJwAyCwEwJoIyQ{@mBuFuLsE_KiA_Co@sAsJiSq@yAyD{HuKmUgUef@yBwEyUeg@cOm[eSkb@gRa_@cXqg@mMsVk[}l@oKkSoA{BsEaJeCsE_G}Kc@}@{E{JeHcOs@_BuA{CyGmOsWem@eCoFae@_fAy_@m|@yKyVeEqJmAsCmIiRwDyIiGiNiH}NwIwOyEmHcDwEsGyIiKcMiLoLoLeK}HuFiTmNeSiMur@ad@sZuRiFkDyCkB}EaDgEsCmLoIg@_@kEmDmA}@{@o@uHmGmLkK{X_YaUuUeo@so@{`@ma@_HcHmAsAcB_BeFgF}GcHiGkG{GeHeMcNoL{NqJuMcIwLkDwFeCmE}CqFgH_NqLmV_F}JgCiF}IsQmIkQ{BaFi@eAQ]yE{J{CcGmByD]w@wCaGwBkEYk@yDaIiB}DwC{Ge@cA{DiJeCwGa@gA}CaJ_C{HuBiHuGcWsA{Fq@yCkBqIAMq@eDoA{G_AqFoM}v@wCiQoCgPkE}WmGu`@uByMsG}c@_Guc@yAwLcCoSyAsMaBsNyBkSOmAQeB]qD_Ds\\\\cGcq@QmBqAoOSoByBeVuB}U}BqWmCeZ]iE]aFg@iJm@{N_@}JYkKQeJKgJEaGC{H?mHBcHLkNL{GRqIDkCN_Fl@{N`AiRrAoQhAyMfAgKzPcxALiAz@gHlBcP^aDjAwK\\\\_Eb@yFb@qG`@wI^}JJgEPmJDoGA_NS_OMkGOwDc@wIo@qKq@iJWaCIq@_@gDcAoIk@mEu@gGoAkJcA_IqCsSkDaW}Lq~@kBsMaEqZUiBS}AGc@}@wGu@iF?EQoAIg@SuAOmAyA}KG]sBgOe@iDuMeaAmFy`@wBcPqCwS_A}GQoAYyBmBiNkBoN}Jyt@a@mCqF_`@qCaTuB}NcAuGc@mCi@kC}@kEeAgEm@wB_A_DeAyCiD}IkDcJsF{NsCoHEK_BgE{A}D}]i_AmNa_@gRcg@uE_MkDcJkReg@eCuGgNq^_Sch@wQue@sIyTyD_KeH{QeCmG_AyB}@mBaAeBmBwCuBiCgCuBmDiCiHwCKEwGyBcCq@qCu@mPcFcCs@gCw@oF_BkAYsLuDyNoEqF{AiIiCw@UeEqAOEuWcIiBk@WIUGuUkHqDgAeCaAsEmCw[eSwD_C{i@c]uJeGSMiDwBeF_DoOoJeMcIm@_@e@WoDwByFoDuQeL}GyEcDkC}BwBi@k@aAcAy@gAkAyA_CaDyA{BoCyEkBsD}@qBiAiCq@iBw@oBiBsFaBqFg@cBkBiIs@oDw@_E_AeGw@aGq@wGc@gF]oFWcFUyGKsGIyKA{@C{Ec@u`AKiYOw[MeXIuGMkGO_FWyGSsEYiFuAuPQaBQiB}@cIu@qFs@iFw@yEeBwJcA}EuA{FG[oHgZe@iBiCwJ}DsO_DaM}@oDm@iCe@qBkIc\\\\eGkVgEuPa@aBiBgH{B}IgEqPs@{CeAaFu@uD}@aF{@qFkByMQuA}@aKw@sKYyFU{FUsGMsGEgG@gGHeNTmIVyGZwH`AsPh@qLh@oO^kLFcBTuHd@_T^kUXa^BsI@kDBcTKeTAqDMqNQsNSiLS}Kg@kQy@qWiBe`@k@aLmAqRqAuRmAgOuBgWCa@S{Bo@gIe@wFw@}Je@sFmFop@gGku@qDcd@OeB_BeS}AcSeBuSgBwT]iEaAyL}A}R_BoSmDie@{@_LgAaOaAyMaC{\\\\mAsPqA_RaB{UsA_SSeCYyEe@kHe@mHKiBcDmf@gCy`@aAoOAMoAmTsA}SyAaVeBaYmJc|AKgBu@uK_Eof@sEac@U{BeDsWmDoWsAmIc@}Cq@cE{@mFu@wEEUiEeVgAoFm@wCmHu^iA}Fe@_Ce@}BCUqDoQeAeF}@}EwDcRwCkNgB}IcAcF}@mEiB_JeCaMeAeFeByI}BeL_EaSs@gD_FiVqHe_@kCsMqByJOw@o@yCgPux@kCwMi@kC}CkOs@mDo@aDu@uDWqAoAoGiA_G{FoY{@yDqBaKaHs]qAuGm@kDUkAoA}HkA}Hu@qFQkAgAqIcA{I_A}Iy@gJu@oJk@aJuAgWCc@{A{ZMoCe@kJg@uKGeAGwAg@oJ{@}PkCqi@eB}]}@}QCi@_@}HoBi`@_Dup@UuEwCam@aCmg@MuC[aGq@sNQqCM}Bg@oHU}CI_Ao@sGcAwJkAqJc@gD}@uGa@aC}AgKaCmMcC_L{CcMcEoO_CiHaEmLiByEyBiFcFoLsFeMuBiF_@aAqAoDkCmHaCoHaGyRcBoGwBwI}CmMEQgAuEuA_GsAeF_@uAo@aCmBiHgDuKqAwDeFyNyAyDyAyDaCwFqBkEc[qs@cSsd@iOg]ue@ufAmNs[qG{NsEgKyE}KaFwMaDiJaAyCo@oB}@wCy@iCsAuEsA_FqAiFqAmF{@uD[wAsAeGwAsGw@aEs@{DeAqG[uB}@sFqAqJy@eGOmA_AkIW{BsB}Ro@_GkF{f@_CmUGk@{AmNcDuZmBaR]eDgAyKs@{GeAmK_AkI{@}H[qCeA}J_@gDOkB[sCM_Ay@}Ge@gDkAwIGk@c@gEY}D[aLMsEIeCEgBAWCw@KwDWiLEsBAkB@yAFuANwALaALe@v@gDhAgDVs@~@eClA_Dp@eBl@_Bz@}B`@cAjA}CrAmDbAmChAyCNa@d@mAXw@jBwExA}DnGwPlBcFdBeEzG}PdAoBh@y@v@eA|@_Al@g@z@k@z@Yj@UlE{AhOwGv@]bIgDdBs@tOgGdMcF|LcF`Bk@tAg@rA_@jCq@lBc@NCbFi@XCz@GtBGbBCn@A`AAhFIrCIbBG~DKlCM`CKpCQvBMlGk@rCc@tCk@nHkBpJaCdCy@`AW`D}@j@QbCm@l@OnCs@ZGzA_@dFoAnGeBhCw@bC}@fFgCtDyBfCqBlCyBnCiCxI{IvD}DzCwCJMdFgF|SaTlCmCzH}HtAwAJMtGyGNORSn@m@p@o@bCeCtM{MhEiE`TeTdQkQ|F}FTUnAoAzD}DvM_NhIeI`ZcZlEmExD{Df@g@nHoHnNoNzC{CdBeBj@m@LKn@o@n@q@dDcDvBaCLOPSvAoBnAqBtAoCrA{CpAwCxI_S|JgUpEcKTi@jSod@zFqMlBiEnXan@hKyUzD{Ix@iB|CcHrWml@dN_[dCiEhAcBhAaBhAyAnAwAlAuAxAsAbB{Az@k@t@i@dBkA`DyBlAw@|@m@|CoB^U~B}AHGlBuAVONKx@i@bBgAxAaA|@m@RMh@]|FqDrDmCvFqDFEvAgAhAcAvAyBt@iBX_AZaAn@{Cj@iCxAoIj@eDn@gD|A_G`B}FNm@~AqF|@}CrEePrIqZnGaU|Ki`@fQgn@nIiZhE}NhIsXdB_G|CmK|AiFHW|@{ChC{IfPwi@xEcP|EmP~AoFtKi^~@}CTy@lBmGlEiOHYt@gC|CkKjA_EnEkOrCqJl_@apAjTgt@`BwFd@qAzBqGHSj@}AN_@tAmDZs@bCwFp_@uy@hFcL`BoDdAcCpAoC|EiKlMcYrf@gfApZqp@`IeQbBqDdCoF|C{GvIkR|DuI`CkFh@iAHOd@cA~BiFhAcCpCcGj@oAhAcClD{HzWsk@zMuYvPq]fDkFfDoFtCwDpCsDt@eAV_@jBeCj@{@x@kArBsC`DmEzJgNdB{BlFoHdGmIbXu^fLwOHIl^}f@lUuYlCsEdC_Fl@_BzD{KbBoGjBiKlAcK`A_Lb@yLPkMRsQPwO`@a^f@a^P_LbAgt@V}PBqCHuGL}IDiCVqUVcQp@mi@FeFJwJJkGJoG\\\\aKh@aLv@eLn@{H~@_IZeCx@qGfGu]d@gCbCsLvFsYLk@jGsX`B{GvBsIxCgJpCaInBuFtAeETo@d@mAdB{D~@qBlBeErEoJzJcQ|HcLb@k@tDqEzMcPbI{JnFwGHIb@e@LOj@y@~C_EdAcBV]dC_EfCkEzCyFbDcHjEeKvA}DfAiD~BkHd@_BtCmJpJk^zCoLv@wCNi@zCiM\\\\uAh@}Bp@wC|EcSf@sBrKaa@xD_OtBaInFwS~Sax@bXgcAb@}AdBoGXcA^yAvBiI`BuFv@iCrDuIvDgHbFqHjC}C~BoCfAmAtAaBdCsCh@m@TWX_@VY|EsFlDsDpA}AnFgHxEsHtCoEpYsd@pJ{OjGgKh@eApByDrB_E~AuCfSa`@~A{Cr@sAhRi^fIwNxEoGd@q@tCgDrFyFvC{CtEeFtMwN|CsDv@_AFGt@w@HOX_@T[nCoD\\\\c@~FeJhAiBnEcHpFcJlGyLz@kBlEwJlWql@|EwK|C_HbF{JnIsNhDkFrMwOTYjM{MtFkGvBgChDqExFsInF}ItAuC`CcFjFgMhEaLvD{JrBsFvA_EnAaD`FeMh@sArGoPxNg`@dAeD`ImVt@eCvK_b@|CyMd@yBbCkL|AyHrE}WjCqPz@iFD[|A{Ib@{BlDyRhGq^jBwJ`EwP`DuL`DgJ`Ls]dBqFlAyDhBuFj_@yiA~Wiy@|CqJ^gANc@dA_DbAwCjLs]dE_MVy@|@iC|AwEvB_Hn@mBL_@To@v@}Bx@aCzAuE`GsQhPcg@nIiWrDsKBI\\\\_Al@mB|AeFpE{MtCoI``@ejAjFsOhXqx@x@eC~@mCbBiF`BoEj@eB@C`CkHJ]Ri@r@sBvCyI`JiXpLy]dSal@rEuMXy@zBqGdEyLvOid@p`@uiA|DeLdHuSNm@dBcHNu@p@_DfAoFvD}QP}@ZyAvAmHbCeMn@yC|@iElB{IxH{_@t@qDx@gENw@nC_NTkAlAeGxAsH~CaPj@sCbBwIP}@b@_CVkAVoAzIub@l@yCdDgPpDsQrLyl@|Iuc@n@cDr@gD~ByLtBaKvCsNnCuMLm@\\\\cBfCwLHa@FYxJsf@x@wDfI}`@bD{OtHo^hNwq@~CuOrDsQTgAtBaKP}@H]Pw@?ErAmGjCkM`@qBrDsQj@oCvFwXlBeJxCsNt@qDbM_n@vBkKJi@DUJe@FW`CqLb@}BZ}AxCeOhSqbAlAcGdCwLjFkW~@oEvDeRvB_KdA_FbEoR\\\\mAdNqg@vBaIj@sBBKV}@HYXeAbHkWxN}h@v@yC~BqIj@qBzEkQ`Qyn@fP{l@dKa_@|]cpAtKg`@lNsf@v@oC^uAjAkEXgAvE{PjNyg@dC_JbAuDbLya@bEaOXeAtA{EnCiJLg@|DePn@qCx@{DjAgGjAoGzB_NlCwNtAsGpKah@pCiNvBeKpC_NbCoLv@yD\\\\cBl@uCjCiMzKmi@zE{UfDqPj@oC`@iBzCsN\\\\aBrKqh@vQs|@hEcUlTuhAt@uDf@gCZwAxAiHBKp@_DhAkFLq@f@iCr@kDLi@DWXwANw@xEiUjCsMh@_CxAsGbAyDvAkF~@gDfAwDz@_DzBqIbCeKn@uC|C{Nh@gCrBaKfBeJr@gDlCoM|AuHtDuQ~@eFl@yDp@{Ej@qFf@sIHoBLcEDoEBgB@mFEsFG}BA{JF}AJqCNaC\\\\cCn@uCd@{A`AeC~@_BdAgAnAq@f@Ql@K|@Aj@Fb@Jn@XbBtAz@v@p@d@f@XpFbFHHvBrBlFfGpBjCnArBfB|DjDfJvIjY|@vChDdI~CzF|A|BzBjCpAhAnAfAnBvAtBnApB~@tEzApMjFbDtAhGzB|CfAfEzA~U|IxB|@NF|HxCbFhBxDvAnElAnCf@vARlCRdCJ|DBv@@|AEvCKxGo@~AObFc@lEa@jJy@bAIv]_DxMmAzCYj_@eDno@_GnFg@jBQ`ZoCx@GxW_CrIw@zUoBrTqBvW}BrMyAvDc@~HaBpJ}Ab@I~NqCpMaC~J}AnCe@jDs@|LcC~Bg@~E{@jAS~Dq@xKaCvA[rEiAhIyBfHuBvIwCjGuB`C_A|@]`MiFxOoHlF}CdLmGjDuBvBuAlPcLpGqE|D}CjDsCxDcDjAaA^[lC{BhAeA`FkE^]|J_JnHwGfGgFhBaBzDmDpCoCxB_CzBgCbC_DnBqC~CuEzCeFnDiGj@eAjCyFhFqL`BuDjCmFbF{KvIkRhXim@dK_U|Wal@tFuLxEiKpUig@zM{YhKyTdTcf@jCcGtBwEt@kBdDkIvBaG~@iC|@}CfDsMjAaFlA_GjAaGn@gDXeBfBeL`BqNjAeKVaCtEac@d@qElHqq@Fg@rD}\\\\jFef@lBmR~Ci]rBeUrBaU\\\\gDVoAN[PSNERQHMDMHUBWBU?]Io@Po@Zq@pAu@bPuIzAqAdB_BfEgEbW}Vn@m@dVoUbKyJxIaJdB_B|A}A|A}A`B_BnBmB|A{AtAuA~A}A`A_AzB{B|@{@rAqApAoAdCeC|BwB`BaBlAmA~@{@t@q@HIBCbAeA`HoG~O}O|AaC|AyCz@uCz@kDxBaJd@wBnK}d@n@iCj@mBf@iAj@mAv@cB`@{@bAkCtAwEf@}Ar@yBZ{@dAiC~ByEvBoE|@kBz@gBtPw]vCcGfa@oz@lCyFpGcNtAwCfCoFbKaTrDyHbEuIZs@jAuBjAoBfAsAnAoA`BmA~A_AfBg@lB]vM_CnLuBdCc@pKeBlb@gHrOmC~AWnAe@|@a@dBq@bKkEvMkFpX{KbNaGpLeGhh@mWjAe@pA_@vCi@tAYtt@cN`@Il@KfB[jTgEnTkD`Fw@di@kIhHqA|Ce@lCa@hDm@hW{D~AUfQqCta@uGxCuAdDeDr@iAbBsCnCuFpEkJx\\\\mr@vBmEbMqWvBsEZq@`Pe]pAqChTmd@jMeWXk@je@{_Abf@y`AdAuBlBiEdBuGbBqGh@gBb@oAP]`R{a@BGnBeEdCmFb@_AzJcTbAkBxAaCzC}DxJqMtPuT`OkRlFcHtAmB@Cd@q@p@{@`@g@v@_AdBeBPQnAeAHIPOdBaBjAaAr@e@XQTKdAYtAOD]B]?[@k@\\\\u@rBkBhF_FjCqCh@u@hCaEjA}AFIfEkGRYbCkDt@kAd@u@b@q@v@wAFGd@{@zBiEp@}Av@eEp@kE\\\\iAhEuKnAoDCUeBqCM_AF}@bAgDlAmDfCcH~@kAr@{@`EgEhJoHpHcFhA_AbAmA`CuDvB}CrAmBdBaCZe@r@{@vAqApAq@nQwI`SwIdEqB|A_AdAy@rKeJxb@_a@lBgBnQePrDuD`FaFvh@cg@~t@cr@fCiCtCaDbEgIjE}M~X}`AJ_@zK{^pAuDzBoFbCsEhR{[l@gAz@uAPYPYLUj@aAbGeKR]v@qAn@}@lAqAp_@m\\\\\\\\Yzh@yd@|UqSj@g@|IwH|@w@jAcAdAiA~bBguBzFmHPU`DwDzDyEnQeUxMyPr@_AnBcCf@m@Z]dRoTlIsJva@gj@rFaL`DsGrK{]bXc{@xJqd@~O{u@pC{MvCeMvHk\\\\bO_s@|EeVnHgTbSaj@hA{CvGkR|@gCdAqCtAsD\\\\}@HWjEmM^eA`KoYLYrEgMhBcFzPue@fIeUTm@Vg@fBoCv@cARS`@a@l@m@bCwBwBeDtLm\\\\rBoHfAsFt@eFn@uG`@uGXaHd@mMDyALsCD_Al@wMZmGBu@RuFHmBQwAc@aDYkCMiBIaBEi@e@mQC_AAi@_@yLWoJI{DCmEHeDVoDnB{RTcCX_D\\\\qDzAoOxAqOnBiSZmD^{DVuCZeD\\\\oDL{AJeAPmBLkA?ErBySX}CBU`@cE@WJw@n@iHn@yGb@}FFcABg@h@eNBi@@[@SBe@RuF@O@[Bk@DgAVgGDiAZFdHnAt@H`@?R?`AQl@WZOvFgCb@S`@SzCyA`D{AFC`@U|BkAU_B?IXMk@kE\"', '2025-12-16 09:48:59'),
(10, 975, 978, 547.70, 428, '\"uwa}Hugi~CMDYFw@Ns@NOD[JSFSFYNKDe@Xy@h@a@TWNWLUJoAb@YDUDeAP_BXaALw@JcBZgARs@LaATkAZ}@Pw@N[FaALOBi@JUD_APkCb@a@DE@oARkDj@]T}B`@_BVaBZgAPq@Ls@NsFv@KBs@HiAPc@Fe@Dm@JQBc@HeAPgAPq@L{@NkAR[Fo@JWFoDl@c@FQDeDj@]DUDa@F}@LQEIGIEWSEEEEECECE?EAE?E@GBEBEDEFCFEHAHGPITS^E?o@LWDyAV{Ex@uAVkARmAPwAV_ALWFa@HUBqCb@o@J_APu@L_BZaEt@_Dj@K@oCd@]Fg@Hq@NmBZwATmAPE?i@Js@L_GdAwAVqATmARc@FSDUDkAP_Fv@i@HWFa@DUD]DcANsC`@qAPgAN]FmANaALKBgANi@HmC\\\\YDOBi@Fk@Hi@FmAPI@[DuBX}Ft@{@L]DiANeH~@{ARSD]DOBk@HeD`@{APi@FOBWB]FkBRmHbAgBTqC^aALeBRWD}ATSB]DkDd@o@HWDeAN_ALcGt@[D_ANUB_Gx@i@FaAL}@Lw@Lo@Xc@Ve@`@sAfAuArAkBfB_LlKgBbBmAhAaA`AuAnAmG`GaB~AsBpBcItHeC~BkCdC_DrCmCjCcC|BIHoNxM_@\\\\{AnA_A`AsArAOLiAfA}@x@{AxAuBtBo@r@]^WVGH_@f@o@v@_AbAIJcBlBc@h@UZy@nA_AzAcF|Ic@x@yBvDmBbDuQb[wJpP}AlCsTz_@aDtFuKxQeCpEwWtc@{@xA{HvMILu@pAyKdRs@lAkAnBmGlKWf@GHaG`Kw@rAeDvFcDtFYf@KPORk@`AS\\\\a@n@yAfC_DjFqGxKS\\\\OVoDfGaA`BADqKpQ[h@m@dAsA|Bo@dAU`@e@v@EFyDzGcDrFGJwAbCsAfCyAzC{C`HeB`EoDdKiDnK{FhQ{ApEuBvGqBdGsHbT_AnCa@fA_@t@m@`AKNEFOTMPQRa@f@o@h@q@h@m@\\\\q@HmAZaALkADsACqAUkAQyIgCqA[mC_AcBy@s@g@g@]yE}CgAu@yDgCkDgCsEqCsBeAkBw@sAe@kAYoCa@uAQkDe@uD_@_COuA?qA?cEDeENuFJsFJsP`@aBD{CFyINmKT_MHsYl@a@@iCJg^p@yFReGL_EH_IPic@~@m@B_IPkKZwAB{HJcJZkDFgGL_@@_@BM?c@@aVj@wINqKZM@aDFqAB[@{BFc@@{BFuADG?kCH}EJiBDmCFoADM?yAByABy@@[@q@@q@@mBFsEL}IToLVgCFmCFwADgA?mABW@i@Da@De@JUFcAX_Bh@cGzBKDSHi@R{DxAKDGBSHc@Py@XsBt@WJ_Bh@yBz@qAd@uEhBcDrAsB~@OHk@^a@\\\\]Z[Zm@n@{BrCo@v@aDbEgB~BwFjHcSxVaFjG{AlBeDlEiAxAgDlEoA~AwDzEqKrN}GzIoHfJwHbKgAvAsRhWs@z@k@v@}C|DMNuCtDUX}AtB_EnFgEnFmE~FqK|MeC|CqZr`@m@r@aEhFkEvFUZaDdEiAvAyAlBe@l@q@z@SX]b@iE~FkExGmFjI{A`CwAzBiAfByAhCgAvBiDjHSd@mG`NwDbImDvH}CdH{@vBiBjEeAtCw@dCc@jBa@|BuAxKeCpUg@nE}BpSmDb\\\\}Dn^UtCCJMdBMxAc@rG_@jJKxBu@vQg@bNaC|l@SfEOnBUvBGb@m@xEc@lCg@lCUfAUhAaAbD{AbEm@vAi@hAm@hAm@dAmAlBqAlBsBlCgF`HaC`D}Xf_@q@|@o@z@qAbBq@z@k@x@mE`GwQlVgFfHeFvGiD~D}A|AcB~AwAlAmA~@eAx@wAbA{BpAcCpAkB`AuBt@{Bn@yD|@iBXcBRs@DwJr@qGb@yDVkW|AmQhAsM`AaAFiJl@eDPoAJsPlAoJl@mXnBmAFmZrBwDVyD\\\\cIh@qEX{BLsBDuB@oBE}@C{@Km@K}Do@}@QkAUeAS{Dy@_@GiDs@wDu@g@Ki@MkBa@mIaBaEw@}Ci@iAQsAMsAKo@E_]iBkUkAsIc@{Kk@mOw@{EUsDO}@AS?{@@cANeCn@mAd@}At@oAx@mAdA_@b@s@v@yAtBkGnJgExGeEnGcKxOeElGkE|G{I|M{FxIsLvQmGtJ_Td\\\\_D|EuEfHqDvFkAdBm@xA_@nAOl@Oz@a@zF{AjUs@dKaAbOYzDiCx]mCl^M|Am@nIcC|\\\\i@pH_@rFcCp^_Cl]aGf|@kG|}@Q`EEt@Y~JS|Fs@lSStGm@dS_@fKYdF[pEk@~Gk@rFSfByAnNUzBk@nFSbB]|CoAbHMlAMtAMpBmArQ_@jFUlDg@lHoC~a@kCv`@IlAa@|HEtACdAc@lYMxIWxOIhHEdCAv@CrCCzACdBAfAAp@Ab@?\\\\IzEChBC~@Ix@UdAe@pBOh@[pAQt@Wt@i@xA}AvDyDhJaA~By@tBSb@g@hAO\\\\oAvCc@`Ay@lBYn@qA~CyEjLeAbCuFrMiCfGgBdEsK|VqAxC{EbLMXQ`@iCbG}BlF}@vB{DhJgAjCIR]v@i@lAiAlCo@|Ai@pAM\\\\mAtCaAzBsCbHsCbHuAlDw@zBwAbDeCzGwGjP]z@iAvCcFdMaCbGaBdEkAvCcOn_@uGnPiUbk@Ob@yG`RqGpSy@fCi@bBaBxEgF|LaBpDuJ`TwBrE}CvG_C~E_@t@qAvCo@fBe@jBc@pBeBjIq@hDMj@Mj@uA`GSbA[nAEV_AdEGV{@xDsAhFs@`Cq@dB_AtBiAlBkAdBkAvAwApAgIhHgDlCi@b@cN|K_GrEgGtEcDdCqF~DsB|AeJ|GmBxAcBxAw@t@a@f@s@z@cAvA}@pAeAhBqAnCiAlCqCzGuAdD[v@aAvBq@rA_AxAs@`Ay@dAaAhAcAbAmAdAUPg@\\\\_Ah@cAb@s@ZOFSF{@RsBTaADeBJeL^qGTcK`@wALeAP_AR_Bh@eAd@_Ab@s@\\\\[Py@f@k@\\\\m@`@q@f@i@f@i@h@oBzBqA~AQV]`@{F~HeBhCgCzDm@~@MZUh@a@jA_@jA_@~ASbASbAKdAMxAWdEEz@Ep@CpACjA@jADvC@z@H~E@f@F`DPnJBbBBtCB~CApA?pACn@Et@Gj@g@hES`By@~FgDnWo@vE{Ffc@yIzo@g@tDs@pEe@zBQr@_AzDsBnIaO~m@{@nDmAjEg@dBuArEw@~By@~B_AbC_AxB]x@[p@sC`Ge@dAg@~@g@~@]n@MTq@bAm@z@c@n@yAhBo@v@q@v@uC|C{@z@{FnFmBdBcGtFuEhEuApA}MbMoAhA}@z@iCjCkAxAiAzAcElGwDxFs@jAg@~@q@tAe@bAkArD}@`Cq@lBm@|A}@`CGX_AzDyBvI]zAqRr{@mExRcSj}@yDbQa@hB_@~AUdAUlAKr@[dDQxBQzBU|DM`EEzC?rB?zABtAJzGPdG?d@BbB@jCEdCGxBQfCW~DIlAGhAKvAk@vJYhE{Bb^UfDOxBwA~U}@vMs@fLwCbc@OzBAPsAdS_@tFaBzUuA|SKvAq@nJWdD[dDk@pDk@dCo@~BeAjD{CtJkIdXoInXuAxEyHxV_@jAqApDaAlBiA|BoA|BiA|AaBnB}VtYuK|LcA|@qAv@g@TiCjAe@P_@Le@NyKlDyFhBcHzBkBl@eBj@wBt@yItCgA^kInCeEvAsHdCyAh@}@^c@FwAXyABm@Es@M}@OcAWwA_@{@SYG}@OiBg@sAa@m@[_@Ws@m@k@_AmAaCg@uAwA_Ei@wAsCaI{AiEKYeGyPeCeHaCaHqDkKoEqN}@wCi@gBy@sCi@qBk@cCgD{NkA_F}@qEqBaKkB{KoAaIoAgHe@mC{CiQaEwUqBgLcF_Z_DyQeGo]eFaZoAyGaCeMmBuIcBeI}GuVwF}SsDwM{Sav@}@gDw@uCmCyJwTyy@{AsF{DuNyGiVuMcf@wAgF}GwVoIcWw@cCg@mAiFqM_IgQsJ_V}GuPwDmJqEkK_]mw@eFaMmAwC_IkRkH}PcFyKoNy\\\\uUoj@aGmNyFyLkGaM}EqIkFqIuCcEcDaFiBqC}GkI_M{M}HyGaLyJmEuDy\\\\}YSQgEwDgVsSuJoI}EgEkUeSwQyOkFsEuVgT_K}I_LyJ{@{@oFmFuDsD{PaQ_D_DcJiJ][m@o@cIiImIgJaDyDmCmDsCsDmC}DkC{DwBmDkEkHaEyH{EqJ}DaJ_I}R_M}ZmSih@kBwEyMa]aNk]iBsEiHwQoEsKiBiEkBcE{EuJ}@gByDgHa@u@GKsAyBoAqBsDeG_KqNsDqEcDcEaEgE}CaDeAgAyCoCcB{AwBkB{BmB{FqEuKcH_L{FuBgAyNqHk^qQsTsKcPeI_OiHgEsBiN_Hu\\\\kPKGkFiCeRkJaJoEqFoCmAk@iOoHwQcJ}M{G_HkDoAo@}T_LgDaB{RyJcB{@cKeF_J_GuF}DgG_F_HiGuAuAqEoEiZyZcMcMuL{Lk`@u`@YYmMsMaGiGiXsXmRuR}DaEoJuJaJgJeEkE_M_MyDwDyOaP{C}CqNyNyS}SkHkHeAcAwEiEiFyEgK_JgHgG}LsKSQoe@ma@eIeH}IyHyIgIsHaHWUwHyHmEuEeCiC}FyGqPoR{AcBkLmMi@o@yRuTkMsNsKuLqKsLwKcM{QqSaAgAkBwBqEeFkHqIyDyEkFoHmJ}Nu@sAkHuMqHgOiIuRcDaJUm@Yw@eAoCaBoE{EwMeIuTgMm]uMo^aO}b@qBkG}GeT{Mob@gE{MyAeFaHqTuGkS{CmJcCwHwB_HsBoGy@kCaDaKwCwH{@wBsDoIuAsC{AcDwE{Iw@qAaCcEkEcHmRgYgL{P_DwEyEsGsE{Fm@y@iEyEgDiD}CwCuCeCoDqCoE}CmFqD}EgC_Ag@gEiBeEgB_C{@iCaAsDiAyBk@}Cs@mCq@yDm@mDg@qCUyCUiCGwAE_DEk@AkEAgC@gAFY@}CNgCNaE^}Gh@wCNoAD_ADiE@yDEuCEsCM_@CqAMcGo@wEo@yEw@}Cq@{C{@]IcDgA_FeBgF}BeCiAmBiAgFyC{FgDcJiFQMMGMIqHqEuE{CqBqA}D_D_EcDeA}@mCgCsCuCuFyFoC_DiC}C}BsCm@w@a@i@yAqBeCmDaDuEcC_ES[gBwCyByDOWcB_Ds@oAaEgHoC_F_@o@q@oA_CgE_BqCsHgNsCkGsA{CwAeDsAqDmAqDuB{Ge@}AyAsFkAyEy@cDgAeFu@{Dy@qEyAcIm@iDcCeNgBuJwA}HiHea@i@sCwGk_@w@mEuEwWoA{GiAkGyBeL}@yD_A{D{BuIk@iBoA{D_CmHqDcJyC}GsDoHaDcGqCmEeCuDgA}AeAyA_C}CkAqA{CkDsDuD{GqGsGoGgEgE{A}AkAoAgBoBmB_C_AoAeC_D}A{B{A}BcCcE_ByCiCgFaAuBuAmD_BwD}AeEM[cAwCsBgG{B{GsGaSiEuMwEeNu@aCu@}BqCsJaCeJyB}IqB{IOq@{BcLeCmMc@wB_@gB_@qBc@}BsAaHsAcIkBmLw@aGq@yFi@_Fy@}Jc@gGGs@]}G]_JKsDQkICeBY}UQ}JCqAKkEScG[wG_@gGc@mGc@cFu@sHq@aGeA{H_AaGcAcGaBeIYmAaBuHmB{GMa@aBwFyAmFwAgEsAuDqAiDgCeGw@aBoDqHsCyF_CkFyCwG_DwGw@cBoCgGqByEeCcGwAqDgBmFkIoU_AkCuAsDcRki@mCqHoBwFe@qAkI_VeIaUeCkHYu@kCgI_AeDmByGsAeFMi@gByH}AsHaAkF]iB]sBqAaIa@kCmBsLgBgLeJol@cBiKaD}PgC{KuEqRk@qBkCyJgCsI}A}EeAwDwDqK_CgG_CuFiCcGiDmHmEwIuB_EuAkCgIoOiIuO}IuPoEmIwBoEiDmHsDmIsEsLgB_F}BwGuBaH_BgFaAaD_CyIuCcLgJ_`@oEwQaOum@kFmT}BqJi[{pAkA_FgH{YcCgLeC{LaCqLeAwFwCsO}BqN_CkNiBwLwBoPw@}F_A{GuBsOqCoSaBuLiEy[qAwJuD_YU}AiAeIwAuKiDgW_AaHsCaTgBsM}AgLs@aFeAuGqAmH]mBc@}BwAmH{CiNgDeN{BoIcCiIoDcLs@uBm@eB_EyKiM}[i@sAyBwF{@uB}BgGkAyCuDqJmNc^yI}TwBuFgGyOyFgOgCgH}CmJ_DwKy@{CcFyR{CmNqCuNoIac@o@aDsHw_@w@}DmBsJ_@yBgF{W_C{LoAuFcAsEiAsEeC{J_AaDqA{EyBiHaCoHw@yByCqIuBuFqByEwD_JiEaJ{CeGgDiGwC_Fk@_AuIoNuGsKkLeRqEuHuC_F[g@}BsD}GaLaIkM}HqMyE}HiAmBuGuKwHkMyM}V}EwJ{ByEwMcYwM{XqEmJgHeOq@uAiEkJwAyCwEwJoIyQ{@mBuFuLsE_KiA_Co@sAsJiSq@yAyD{HuKmUgUef@yBwEyUeg@cOm[eSkb@gRa_@cXqg@mMsVk[}l@oKkSoA{BsEaJeCsE_G}Kc@}@{E{JeHcOs@_BuA{CyGmOsWem@eCoFae@_fAy_@m|@yKyVeEqJmAsCmIiRwDyIiGiNiH}NwIwOyEmHcDwEsGyIiKcMiLoLoLeK}HuFiTmNeSiMur@ad@sZuRiFkDyCkB}EaDgEsCmLoIg@_@kEmDmA}@{@o@uHmGmLkK{X_YaUuUeo@so@{`@ma@_HcHmAsAcB_BeFgF}GcHiGkG{GeHeMcNoL{NqJuMcIwLkDwFeCmE}CqFgH_NqLmV_F}JgCiF}IsQmIkQ{BaFi@eAQ]yE{J{CcGmByD]w@wCaGwBkEYk@yDaIiB}DwC{Ge@cA{DiJeCwGa@gA}CaJ_C{HuBiHuGcWsA{Fq@yCkBqIAMq@eDoA{G_AqFoM}v@wCiQoCgPkE}WmGu`@uByMsG}c@_Guc@yAwLcCoSyAsMaBsNyBkSOmAQeB]qD_Ds\\\\cGcq@QmBqAoOSoByBeVuB}U}BqWmCeZ]iE]aFg@iJm@{N_@}JYkKQeJKgJEaGC{H?mHBcHLkNL{GRqIDkCN_Fl@{N`AiRrAoQhAyMfAgKzPcxALiAz@gHlBcP^aDjAwK\\\\_Eb@yFb@qG`@wI^}JJgEPmJDoGA_NS_OMkGOwDc@wIo@qKq@iJWaCIq@_@gDcAoIk@mEu@gGoAkJcA_IqCsSkDaW}Lq~@kBsMaEqZUiBS}AGc@}@wGu@iF?EQoAIg@SuAOmAyA}KG]sBgOe@iDuMeaAmFy`@wBcPqCwS_A}GQoAYyBmBiNkBoN}Jyt@a@mCqF_`@qCaTuB}NcAuGc@mCi@kC}@kEeAgEm@wB_A_DeAyCiD}IkDcJsF{NsCoHEK_BgE{A}D}]i_AmNa_@gRcg@uE_MkDcJkReg@eCuGgNq^_Sch@wQue@sIyTyD_KeH{QeCmG_AyB}@mBaAeBmBwCuBiCgCuBmDiCiHwCKEwGyBcCq@qCu@mPcFcCs@gCw@oF_BkAYsLuDyNoEqF{AiIiCw@UeEqAOEuWcIiBk@WIUGuUkHqDgAeCaAsEmCw[eSwD_C{i@c]uJeGSMiDwBeF_DoOoJeMcIm@_@e@WoDwByFoDuQeL}GyEcDkC}BwBi@k@aAcAy@gAkAyA_CaDyA{BoCyEkBsD}@qBiAiCq@iBw@oBiBsFaBqFg@cBkBiIs@oDw@_E_AeGw@aGq@wGc@gF]oFWcFUyGKsGIyKA{@C{Ec@u`AKiYOw[MeXIuGMkGO_FWyGSsEYiFuAuPQaBQiB}@cIu@qFs@iFw@yEeBwJcA}EuA{FG[oHgZe@iBiCwJ}DsO_DaM}@oDm@iCe@qBkIc\\\\eGkVgEuPa@aBiBgH{B}IgEqPs@{CqBeIoCoKqB_IwGwWuFwTa@yAaCcIaBaFoAsD}@aCy@qBiAuCmAsCwAcD_FqJmEqH}@oAmFwHwHaJqFmFsFuEyF_EeEeCgB{@wBcAqNwFuH}C{Ai@aFmBkNmFa@OwEmBwCcBgBqAsBaBwB}BkAcB{@mAmBeD_AoBcA}Bi@yAcB}EkCqHiM}_@cBgF}@kCY}@uC{IiGcR{]oeAeAaDSm@cBiFoAqEEKo@eCmCiMu@mEgBeLkByLkCyP}QslAQcAi@sDa@gCs@{EgAqHiBeL}@_GiDyTqDeVoFm]_AoFmAqFcEuPyB_IqB{Gu@sBoCgH{AgDoBaEaD{FiDiFsAkBiA_BkJmMuAoBeAyAoCuDwb@{l@gCiD}IcMgA{A_GeIoAeB}@oAqIqLsG_JyXc`@{@kAkD_FaDqE{AoBsBqCeFcHiFiHsHeK{G}IeAyAyI{LGIcJ_MiDwE}CgEuAmBcAuAoCgDuG{IsGcJqA_Bo@}@_EuF_@i@q@y@a@i@aE_GSUKOKMS[{@mAGIQYOSMQsAgBc@o@}@mAm[yb@eGoIg@o@_@c@[g@KKm@u@gBaC_G}HsFqH_ByBwHeKc@m@sGiJo@}@q@aA[c@W]iE_GiDuE{MqQy@mAqDqFyA_CaDsFoBuDwAqCyA_DcA_CqC{GgAsCoAmDm@_BO_@wA{EoCaK_AwDaC{LkBoIEU{EsUWoAwAcH_ByIwAqHkCmMw@iEi@iCuBoKkEaTkByIcBuIo@oCOk@g@qBaA_E_A_DK_@{AqEwAqDeAgC}AwDo@wAaFwKkAmCiC_GgGcNs@{AeCsF_FuKIMeAaCKSsBwEsBqEcA{BsDgIaIqQ}HeQwBkEgBcDkAmBeBmCyAwBgBwBeAmAi@o@}@eAcBeBmAkA_CsB{EiEkD}C_DkCwBgBsC_DYYiGwHwAiByEiFm@qAQi@Me@EYE_@A]?_@@_@Fa@DWDSHUL[JQPSTQPKNEN?R@TJRTPXL`@Jb@Hp@F~@BbACzBMlBE|@UnEARGjAw@tSCv@q@jOw@`NyA|X[rFg@|Hg@dGa@rD}@lHm@~Di@~Ck@zCyAxG{@nD_ApDsApEo@nB{@~B}@bCcAdCy@zA}@hB_BfDgA`CsA`CaA|AeAjBwCnEaBxBqAdBkAvAiElEyAzAcAz@UT_ExCeErCiFtCgEhB_XhKwEhBcA`@}c@nQuB|@{B`AwCnAcKfEkFzBkFxBoJvDcBj@aD`AUFmBn@kDlAmJjDua@hP{PxGoHzCmJnDQFyCnA}DxAmHnCcE|A}EpAqDr@mFn@cBBoBBqBAgEQoCWsCa@gBa@gCk@uFwByIiD{DyAeFkBgDqAgHeCc@OsDqA}FsB}D}AcBs@aDqAaD{AoG_DsE}BkGeDqBqAiFkDwAaA}AcAu@i@yGwEkHwFqCwBuFwEgLsKsIcJgFaGeBsBkDcEaD_E_AoA[c@_DiEsHoKgBoCiBkCQYiBiCIKgCoDoAoB_B{B}@oAgEiGwH}KaCmDa@k@_GsIWa@cBcC_AmAcCeDqCyDGMyDsF_GwIqC{DiCwD_GuIq@_A}BeD{CeE{BcDqAmB}@oAk@aAq@kAkA{BeD_H{ByF_DwI{AeFuA{EqAoFgAmFm@_Di@_DMcAQgB}AiOg@}Fq@cJWaEQgEMqDIoDq@y]_Aun@KyEGwDc@iZG{DCqBWuOO{KUu\\\\CoCCqBE}IQyLCkCWgOMqHWcQa@{VOmJG}DEiBFyA@e@Bm@B_@De@HoAB]@a@?]E[C[Gy@IeAEmAA{@CsCCyA?m@Bk@Fq@Fq@`@cCh@aD@MBU@c@Aq@m@Dk@DyBP}@JkC`@mA`@cBt@iFdCkAb@e@Ly@Pw@HmAFyA?a@E{AKYCYCWCkBQaAEo@Cm@@u@Fq@Lo@Ro@Vi@\\\\k@d@gCjCmA~AoAbBqBfCaA~@o@f@YLOH]Tu@b@e@TWL{@^aAZG@m@N_BTA@UBqBFIAoAAaAG}@KmBc@iA_@YOq@Yu@_@o@a@c@[iA}@oAgA}BwBwDcDiDeDy@m@y@k@eBgAkAk@i@Y{Ao@aAYaA[aAQiAQu@Ku@KaAMw@Em@Cm@Au@?m@@o@@o@Ds@D_AJeAN{@NqA\\\\kA\\\\aF|AqDfAe@FsAR}@LsAHmADkA@uAEkAIgAMu@I{@QgAWy@SaBm@wAq@sAq@eAq@mA{@s@m@y@w@_Ay@kDqDQQOOiDkDoJ}JiDsDeDqDaDgDwG{GcCiCqDwD{AaBmHuHcLqLaDsDkHeKoAmBqAoBaAoAmBaCkBmBeBsA{AcAmCqAsC_AwB_@iDW_DIc@Bm@@_@@y@B}@DeDEcAAe@Cw@CkBQuCKaBQuCe@_BW}Bg@aKeBiAUoCg@oEkA_@O]Kc@Oc@O_@Qg@WOGKEoAa@wD}AyDiAsBc@wBYwHi@oAQqA[yAe@gBy@uAo@aAaAY][g@Wq@c@uAk@yBSg@]u@gC_F{@{B[kAO_AIs@EaA?w@Bo@HgCt@_Hz@kGvBaOrBgO`AcHrC_UzCkTlCsUz@qJv@sK`@kHb@mI^kI`@uLVmJL}HFcIB_KEcKGeFSuP]uZQeOm@q`@]sOs@gPgAkOcBeO}Em^{B}O}BsPkCuRo@wEq@gFsAaMy@sJc@uF_@iHUeFOoFi@m[c@yQi@gRm@cSu@aQw@}P]oKO{FGmCC{AE_DAeBAuC?i@BeB_@cDEe@GaACwA?sB?k@FoW@oE@_B@yA@eEDyJ?oB@WBgNXeBJYPWTUXOVEVDTNRVNTNf@Hh@@j@?n@E^Id@O^OVQP]LW?k@AQIgCqAoAq@{BuA}CiBoBsA{EuDqCyBcE}DaA}@wC{CiEiF}DcFaCaD{A_CyA_CqAaCoCcF_DgGiCsFoAwCw@iB{B_G{Rui@mG_Q_Pcc@mAiDsOmb@}Yix@}FgPk_@ceA}AwEa@oAmDeLaAaDcDgLaCmI_@yA_HiWm@{BYgAgAaEcEsOyBkHuBoGiEkLkEeKsCiGg@_AyBgEcCqEmCmEeG{IsGcIwDmEkDiDuFuEiAeAmI{F}CsBuBgAcCcAwD}AaG{BuLmDcCo@{I_BqGc@{Fa@iJe@}Kk@oDQyUmAaKi@qCMu@EwDQa@C{CMeBI{ZwA_UcAqIa@uKg@{@CmAIgJc@_Ia@kBIc@EgEa@oGoAgDy@wBi@aBg@yCkA}@]iJyEQKoGeDkB_A{OiIyDqBWMaAe@eCsAuBgA{Aq@{DwAgBi@o@S}Bc@{GqA_Ec@yDUqAGkCEwBBM?oD^}ARoBXkBXmBZq@HsC`@{E|@mC\\\\{Cj@iCf@_C`@{H|Aa@JqHfAcGhA_GjAgEf@kCTmBFwBHuBHkBCkF[wBWmJkA{XmEiCi@qCq@}Ai@cA[wAk@aCkAyA}@{@i@yAaA_Au@oByAwDoCwCeCkBuAmBoAaE{C[W_FyD{DuDgD}Ce@e@uCwC}G}FwEmE}@u@w@o@aFuDaDqB_EgBwFsBw@QmMcCgF{@}@OSCw\\\\yFiAWgEeAsBi@SIa@QmAi@oCiBs@k@cEgDgBsAmGiFkDqCsCmBoAs@i@[}@g@q@_@y@a@gE_Bu@UoDeAyC{@oCw@gE}Ac@S{Au@eAq@kAcAk@i@uAsAyBwBcAiByGsKeA_BoA}Aw@y@qJeIyEsBiAk@sAk@uCy@aDy@sGqA{KkBgBa@sGgBo@QoC}@cQyEuOcF}DuB}B}AcEkDmKgJoC{Bm@]}A}@a@OuAg@eBs@mCs@y@OmB]_J]mIS{CSoASwAYmAc@g@W_@QaBs@uBaAaAe@]Ya@Yi@g@}O_PyFqGmAiAuBiB{@y@{AkAMIaE}ByBsAqBgAuAs@{CeBmC{AWOyCqAiA_@kA]}ASw@CwBOiAGYCsDSaJq@s@GwE]iNy@o@EkRkAkYoAmJPcJPcGHmMLgDBmD?sDD_EDsBE_JEw@@}CDcDDkA?cGFgA?aB?kAAyGEuFEgGAyBCeE_@sHuAqImB_JmByFqAsAY_AU{@Q{Cs@]IqCg@oCi@kCo@cBWcC]SAoBKyDGeFCaDKiE]uCs@mAg@iCoAoCqBiE}DwDiDuByAoAu@eBy@mAi@}Ae@kAUwAUcBIoFJkRx@wCNiI^}EPgFN}@BuDXkCLiCL{DR{CZ{BX}Bb@uBj@mBl@aBh@mBv@iD|AsDxBiBzAqCtBq^t]wCzBqAn@iB`AoBl@kBd@WD}@RkAV]F}Dp@mEt@}@Ng@J}Bb@_GdA_CPyBFmBGoFi@kA[kAWaA[cAa@wAq@}BsAoBwAeBaBuBsBeCoCcFkFcJ}JoLiMyMuNcGqGeDuD{CeDe@i@eFoF}DiEmCuCeJ}JsJkKkEyEaA}@aA{@cBmA}A{@cB{@kBq@_Bc@iC]mBUiBOeC?wBPg@H_BV_Dz@aC~@uBx@qAh@_@PoB|@yB|@eA^aC`A}F~BuEpBiDvAgGdDwBdAqAh@]N_Bn@iAb@mGhCkP|Gm@VqFxBuL`FaKfEoLrE}CtAcGfD_EhCiAbAkC|BgA~@yCpCaA`AuBdCoA`BsAjBgExGwCbFmBrDiBvD{ChHeAdCuEvKiGtNwUfj@MXEJ{D|I_BfDcCpEcDxEkDnEmBlBkB~AaCfBaCvAc@XsAn@iAj@k@TqAd@y@T_ARqBX{BVqCPgCBmA@kBAeCYcCa@gB]iBg@{@]g@U{C_BcFsCaN{HeT{LeKyFKIuBmAgAm@y@a@WOME[KwA_@MEk@MyDy@WGm@MeFkAQEGAiAUsK}BoEcAcB[iAUkAUmAS{@GmA@_@@cBHa@@gDHW@Y@c@BuBHiCL]@qADyADoAFiDLuHXuV~@]@q^rAmBCcAO{@SgSeGmIcC_I{Bw@Y}@U_Be@oBi@qBi@eTuGaJqCcFyAyGmBq@U}Bu@cCuAmEkCcB{@kAa@YK[KmAc@aZkJ}UsHyHcCm@SaKaDmGqB}Bu@cDaAyS_HQGkIaCeMcEoBm@}E_BkA_@gH}BOGsGkBqGwBu@WkD_AyAi@w@Sk@UwDwAq@UYKcCw@uKeDyBu@iBk@cCu@}IuC{C_AwCcAo@SeGkByYmJqLwDuBo@eBi@qGwBq\\\\sKyNqE}J_Ds@UoAs@iA}@}BqB{@w@uCgCcSiQyJkIsBgBeVsTyAoAwEeEa@]_@]kAeAqBkBaCwB_Ay@SS{BkBiB_By@u@yZcX{DiD}EiEc@_@_CqByE_E_PmNgGmF_FiEyBsBsBaC}EaG}@sAgA{AaA}A[i@{AcCqCyFoCwGqAmD}@eC_HoSwDaLqCmIuCyImEmLeBqDyAcDcBuC_DkFmGwIeDcEiCiDyMqQqDiF[a@gCeDgDqEwEmGuGqIwLgP_DgEoCsDm@y@i@s@wEmGeDoEa@m@uEkGuC{D]e@}@iAsD_F_BwB]e@eHkJ{`@ki@wCcEuOwTsKkNqHiKaByBuFqH[_@kDkE{AsBgEcGc@m@yEqGgAyAwDeFkBcC_HmJ}HaKuH_K]c@uOuS}k@{w@oFmHee@yn@eGqIsAoBsAuBk@eA}BaEm@cAqK{P}@{AsG_LgJaP_DiFeCeEu@oA_@m@gIiNw@sAuBqDeAkBe@y@c@s@qCwE_Xad@sAyBkCwEeCiE]k@m@cAe@w@Uc@wAaCCGmAqBoBcDMSeByCkAoBq@kAmCyEcDuF]k@iCmEK_@GYEY@QAQCWGSKMMGMAKDSGQKWWgBwCKQwN_V{@{AuBsEe@eAcEoLeFkNoBmFyGmRg@wA_EcL}DeLO_@_DcJ_EwKiAeDaBsE_CmGe@sAeA{CoGoPeGyPyD_LgH{SsEaMoBuFsKg[eAuCYy@eCkHaIsT_@eAwEyM{AiEkF{NYy@IUkAgDwGiRqMu^wA_EmBuFm@cBwA}DCKc@kA{JsXmAaEc@gB_@oB_@kCYeCKeA_Fmi@wCg\\\\{Eai@i@kGSoBcCmWmDia@uA}OI}@SsBa@qFOcDC_B?oADsDJqG^w_@p@cXZ{RTwNd@i[XuP@_@TwFJmEHkDXgQBoE@eCHoFAyBH_Fl@ea@f@w\\\\PgMfByqAl@wa@h@_`@b@o\\\\XoQVkSd@mZL{J?}BGiDMiDScDUkEk@{JKqBkCsd@uBsa@k@kKUoGKyGZkd@HcT@aB?{@@oAFsNHoPPcLZok@BoFLoX^{p@JsTL}ZRq^P_d@Rsi@?aBVyr@T_k@DiHPkWH_S@mAn@g~ANg[p@oyAPs_@Hs[TuLl@mTnB}s@rAkg@FyB`@_Of@oP~B{z@`Ci~@d@kM^yM^_O`@aQDuAb@kN|@qZf@}Od@oPh@sR`@mMb@}OJqF@cC?iCGyGMqDU_Fc@yFc@aEWoCkAqKkC_VsAmLQ}AAMaBsNa@_EWiCoCgWcE}^o@sF{BwSUoBcCoT}Fwh@MsAsCmWKiAcEm^o@{FE_@_DqY_Es^wJo}@ePazA_@gDy@sH_Eg_@aE{XqCsOGa@_DgNsGkV_Nef@]qA_@qAi@kBwHsWkF}NoHiO}EmHQW{CuDaHqHib@ac@gWwWeVyVWWkBoBcDiDgOuOq@q@}FkGcD}CuDsDoBgB}FcFWWcC}BwAoAg@c@i@i@c@a@iIuH{BuB{G{E_CuAuBeAw@_@yUyGiCw@mF_BuF_BkGeBuS_GmA]uIcCkA[iBi@eCu@oEmAoEoA_NuD_F}AkCqA_Bu@wAcAuCaCIIyBcCkCgDo@gAsBmDk@iAmDcHoFoKoVcf@wAuCqSia@oCmFm@kAe@}@kDyGwCeG_B_DyKiTmPc\\\\_E}H{@cB}A}C_FwJa@w@IOwBcEO[eAuBuDoHWg@iCkFwFqKoEwImBcEcAoBgHcN_DuGy@aBiBkDu@}Ae@}@{CiGeEoI{@eBiA}BmBmCkBiBuBiB{FiEaCkBwBcBsEgD_As@g@]iAm@q@[_AWoB[UAy@GkBAg@@g@Bo@B_BLSBw@AWF{@Nu@VqBz@}BbAsCnA{CvAgC|A}FfC_@Pm@T[N}BbAoJdEsAd@kAb@sAVcAH}AC{F]e@G_LwA}Ek@aYuC_CWy@K_AIc@CIAkI{@gBUmA]a@S]Sm@a@MQQGSBQNe@Ne@Ru@H{NgBWCw@GgCWgQgByDa@}@KeCWqD]ap@cH}Fm@qf@oFcGo@cNuA_AKqMuAkV{BgBQ{Ec@aUaCsMsAyFm@er@kHqAOe@CoAKiBO{HIcFHuEXyEh@mDf@a@FWDaFbAaDdAiGpBkYxJe@PwQdGeGrByMnEuJbDkDv@yDh@}CZs@HyABsBEwDKuGYgBImAIgAE{FWy@CgIo@cGw@s@K_IcA]Ek@KsNaCeNkD{L}C{u@mRki@aNeAWiIkBcDu@{Cg@k\\\\iDsN}Asc@}EeNyAyC]_RmB{T}Bq@IqJcAwAMcUwAqPgAcPoAaNgAoUaBmQoAgCQaIe@{Jo@}Jo@_N{@cAIeAIyE]yXqBkVcBia@sCuTaBqHk@iM}@gCI{H@wIf@mOfAuDX{NhA}@FwGd@mEZsGb@kGd@mRhAiCJgD?mDOcDGq@A_FUYAu@GaKc@mLg@cI]aDKK?cI[e@A_@AcEO}J[eOe@wIW}J[sGS]Ac[}@iFSuGQgAGoDOkBG{NM}Gb@iEv@iEv@g@LiDbA{Bt@ePzF{HnCmD|@{AZu@HsGr@mFD{FGqo@sAkQYsa@s@cBGoWs@w@Cs@Ai@AcBCa@?kAAi@?qDKcM[iGKsGE}LYeCE_LQwGOkIOcJOsKU_EOsCYkC]{EaAqTcEyUqEk@KuH}Aeg@mJkNaCgNaCUGs@OyPcDyCk@uMeCePcDuPyCcBQiFe@kFQqAE{Sg@_AE_@?oUq@eDWuD_@sCg@eASkD}@eDqAg@Ug@QqQyGkGiC}B{@cBm@eNaF}IwCqA_@[KMCwBo@gCi@mE}@uH_BKAYGsQoDaLwB{E_AsDu@yGwAuJmB_GeAeFeAqEaA[GKCw@QYIQCsE_AeCm@kBe@uAUw@I{AGq@?Q?k@@wAHyATaCj@o@Rw@RsA^yCv@q@PcBb@]J{Cn@sFvAqIvBmCv@uBp@cDvAkBbAyAr@{@ZiAVgALoABiCQ}A]cBs@sDwBiHcEu@a@s@e@iAs@a@Wo@a@eJsF_CwAoEmCuEqCcGoDuHuEaF{CkAq@gJsF}MiIMIaM}HyJuF{@i@uOkJmLeHgIaFmAu@gKiGaCwA_@UUMaf@{YcSoLyEoCw@c@eB_AyDiB]O}HuCiCk@wCo@mHyA{NqCcFeA_LwB}@QkFgAa@IwBa@oDq@qJiBUGo@M}@Qe@IcASoI_B{@OkCc@uB_@sIaB{GoAwFeA_Ca@_DW{CIY?{AAsEJeE`@gAPk@JwBb@WHk@L]JYJ[HoA`@wJdDqNvFgBl@g@PaBl@qRfHaFfB_FtBuAp@oAr@uCjBqCxBaZ|XyThSeEbEyVzUsBfBiB~A_GxE_D|ByAfA{GjEm`@~V}FpDoD|BsIjFe@\\\\oA|@y@h@mJfGeHtEyDhCoBtAwA`AoCjBgEfC}AfAeAl@yWzPgJbGuDbCgN~IkBjAeGbEiAr@wClBgC~AsE`Du@f@{@l@o@b@yCxBqFrD_AJQYa@K[HQ\\\\Id@CZ?`@_@fAuDlCuB|AkGtEeGpEkE`DcGlEoA~@qU~PmDpCk@f@w@r@s@l@[ZgDvC?@eB~Ag@d@uApAQNo@f@m@f@yBhBoC|BKHi@NO?KAUEUEYIOKUCY@OHIJEFOXI`@Ed@Ad@@b@F^JZNXNPPJTRJLLXVx@d@pCBTFh@BNJr@BRRvAf@nDbAnHTxB^xBXvA|AbGHZf@jBNh@n@|BLb@XbAnAnElA|EzAvGbElTj@nCmItHgPbOsHtHkInHuFrF{AzAq@|@w@tAm@rAq@pBu@dC{@~Ci@~Ci@dEuDlYo@jEs@fD_AfDwAdDeAfBoBhCyFrEuL|IwEfDg[pTgLhIyHvFaChBcMxIqA`AaBjAuF|Ds@hAYb@oBnDkCtFi@lAiKzTqArC_@v@a@x@gCtFABMTA@{CpGqCbGUd@GLOZmBdEgEhJMTCDqGhNqCxFcGzL_@l@kD`EwBlCWZGFyCpDc@b@uAv@KBG?EAMAMGIQQOSASHKLIREVAVIz@OZeAtA_FnGoCnDqCnE{B~DoCzEm@jAeAlBeAzBcC`FqHpNeF`KYf@k@h@[V_@JqJxAe@BUU}@aAuB_CKO}BgCiHuH_AeAKMYWKd@VZlAlADL\"', '2025-12-16 09:54:00'),
(11, 986, 975, 41.72, 43, '\"{qg}HqxyzCLKg@uBGYm@d@i@b@IFKHCGCEECE?GBAIEUqAaGOo@Ia@oAwFCOm@mCgA}ESaAq@_DI]S_AYkAg@}B_AiEY{A[kAqBcJMi@y@yDKm@WaBcBmNIo@Is@oAcJq@sFw@yFGe@o@yEc@yCQ{AiBkMyEq^]uCQwBMuBMoEi@uRGg@CKEIEGIK[SUGBu@@a@RuH@oA@qC@{CHmUNei@Lse@BcKHgd@DmJ@uCB{JDgK@{DBkF^w~@AiGEcHEeBEoBM_EIcBKeBKeBMgBQ_Ca@qFc@uFKgA[eEc@{G]gGe@oK]sJMwEIyCOkJOaMEsFAaGD_j@@gFLkrA?yD?eBFak@Fsm@@wF@}u@?gJ?oD?eCAeC?uA?a@CaIWqe@GkJIcMGaLAqA?a@Sia@k@ev@[ub@_@wl@E_HGgIAeBAqA?qBA_@?aAAkDQaS]uj@GwJKyP?aGJsIR_JxD{pAbAi^v@cXz@_Z`Aa[d@_ODu@LyEPwG|Aqh@vA{d@t@kWb@uMHaDv@kWJqD`@aNh@}PbBcl@@a@TsHf@_Qd@uPvAie@r@iXToHLeDbAg`@~Akl@ReH`Bym@p@mXb@gPHiCx@uYHuB^yMBs@DcBF{ALyDLeDBgBDsAFkEDwCBkA@cB@qCB{C?kDAeCCwBQyIQoJImCMwCUmEi@gIm@aIc@qEQaBQcBs@oFwA_KkByL{BgNEUuA_JZ]TWj@o@rCeDhH}H~BaCtD}DfAqAtBiClAsA`JqJlWgYdBmBbBkBbDkDl@u@P[P[FoA@mAo@mCI_@c@uBOy@WqASmAGe@C]@[De@N]^o@b@y@L]Pa@Ts@bAsCHUDMtBiGt@uBv@_CX_ADWR{@FWDWHa@TgAn@yDh@}DPuAt@cGtCuVxAmM@GhD}YHu@Lg@N]d@q@`@]XYf@m@Xi@Nk@Ju@BmAFmCHw@r@cD^gBfBsI|DeQh@}A`@s@`DaFnD}GfAkBz@{AnB_DxEoIbAgBr@qArCcFrDkHrDkHDITc@h@cAZq@p@sAv@{Ah@gAHQZm@v@{A@EBEfAyBP]^u@BEdAuBf@eAHOhB_ErEyI~@gBR_@Ve@UoCAOC]_@uESiCo@cIuAyQKsA[}DW}CAUAIQmDAQEeC?kCF_ETwGNoCDkAD}AAgDGwDKyIGeDK}E?aAC{@EmAEy@Gs@Gw@KkAKmAQsBEe@QyAk@uF[_EEyBAqAAqABsDH}BDs@Fy@r@{H@O\\\\_EPcBT}B@KBYVsCBSh@uFR}BR{BbDy]Fa@PoBRqBBWDg@DYJu@@I@EJm@jA}F?AT}@@It@mD`BkH`@_BBI`@mBj@cCJe@lBuIDQNk@x@mDjA}FPwANqAT{C@URmDHkBFcAJwAHgABeAB]FkALkC@QL{BL{B^gGDaAJuBRsDFwAFcC@U?gA@eACsAAo@KaEE}AOcHE{@As@AGQcJOoFI_CE}AA]IsCMwHB{@?IFoEJgHDeDFgD?EBeBLeI@}@?GDiC@WBcBFk@BwADmDD_CCkABoADeB?W?KBkBFkEDcC@u@@m@B{ADcDDiBB{BB}A@gA?ODaC@_@BcCF_D?Y@k@@eADoB@eB@qB@g@@_@DcCDqC?]BuADwC@e@BgAHiE?UFkD?[Ds@[Sg@_@s@o@QOk@c@]i@]sAk@qCQy@gAcGW{AOw@G[EYc@Va@RKFEBsAn@_Bh@KB\"', '2025-12-16 10:18:34');
INSERT INTO `calculated_routes` (`id`, `from_office_id`, `to_office_id`, `distance_km`, `duration_min`, `route_data`, `created_at`) VALUES
(12, 826, 1001, 355.32, 327, '\"uwa}Hugi~CMDYFw@Ns@NOD[JSFSFYNKDe@Xy@h@a@TWNWLUJoAb@YDUDeAP_BXaALw@JcBZgARs@LaATkAZ}@Pw@N[FaALOBi@JUD_APkCb@a@DE@oARkDj@]T}B`@_BVaBZgAPq@Ls@NsFv@KBs@HiAPc@Fe@Dm@JQBc@HeAPgAPq@L{@NkAR[Fo@JWFoDl@c@FQDeDj@]DUDa@F}@LQEIGIEWSEEEEECECE?EAE?E@GBEBEDEFCFEHAHGPITS^E?o@LWDyAV{Ex@uAVkARmAPwAV_ALWFa@HUBqCb@o@J_APu@L_BZaEt@_Dj@K@oCd@]Fg@Hq@NmBZwATmAPE?i@Js@L_GdAwAVqATmARc@FSDUDkAP_Fv@i@HWFa@DUD]DcANsC`@qAPgAN]FmANaALKBgANi@HmC\\\\YDOBi@Fk@Hi@FmAPI@[DuBX}Ft@{@L]DiANeH~@{ARSD]DOBk@HeD`@{APi@FOBWB]FkBRmHbAgBTqC^aALeBRWD}ATSB]DkDd@o@HWDeAN_ALcGt@[D_ANUB_Gx@i@FaAL}@Lw@Lo@Xc@Ve@`@sAfAuArAkBfB_LlKgBbBaBp@k@Pm@Lk@Bo@?m@K_@Kc@Sq@c@u@q@m@s@gC}CoAkBaAgBcAiB}@cCwAqF_AaFcCgM}BuLuAaH_BsGiC_JyCiIuDqIyA}C}@aBy@uAy@iAa@m@_@g@qDuE}@iAeAiAiGqGeMsLaF}EuEsEyIqImd@kc@gH}GQSMK}A{A}JoJ}E{EwNmNm@k@aA_Ao@o@k@g@oDkDq@q@_MuLaG}FgCiCsB_CeCcD_B_C{AcCsAcCmAaCoAqCaBsDcBiEqBiG_B_GkA{Ew@qDq@iDo@eD[yBaA}HeCwT_C_T_CyRsCiWcAkJQ{AyFyg@s@iG{@wHc@{Dg@cEMmAa@mD_CyScDsYcEe`@QaBoOitAkHqo@iFme@cAkJ{CqWyHgr@eAkJoDg[iBiPuDw\\\\eAiJaLecAcBeOGi@u@sGg@kEg@iEgC{TI}@MgACW_CkTsHkr@mFoe@gCgUyKubA{@{HkM}kA{Da^}AyNmAqKeAcJaB_OMgAkCeVgG}j@iBkQSkBmDi]{Gqp@[{CCOaBgPqKudAMuA_Eq`@i@iFSsBm@aG_A_JWiCq@qGiAsKaAgJwA_Ni@{Dq@}DeBcJ_BeH_AqDaA_DoBwFq@kB_@_A}AwDgAgCmA_CaDkFoDcGsRq\\\\kKsQkBaDwGaLgP}Xo@gAeD_GaAgBy@eBcByD}@eCg@oA_AsCkAwDmB}GiAgFsA_HgA}GQkAOcAUkB_@yCs@uHe@sH{AcUu@}MuBo\\\\_D_g@sAeTyAiUQuCiDoj@uAsRyAqRGcAQoBKoAoAuOsAsPcAoMyDye@k@gHqBsVOcBIgAsFur@OuBi@aHg@sGScCc@yFeGav@kCo]sCc^aCiZiFop@aEkg@{Ck`@oGyu@wKmqAmF}n@[gFMiCMiCOgEGgCGsCCoDAcC@mFFoIVmR\\\\}XVmSDaEPoMlAs`AFwDDsD?OHiF@iBLsIJcJT}QLsKPkNNsHBsABeAH}D`@{Kf@uJj@}Jd@yFxDub@`@iEnDa`@T}BhFsk@v@mIvE}g@rAgMtBqRPyApNipAp@{FxHor@jEi`@XaCxEqb@vH}q@n@yFNqAz@}HbFcd@xDi]dAkJtLmfAxBaSfJky@bAeJ|E_c@|H}r@rO_vAdEi_@h@sEfHqo@nJc{@dDiZvEcb@f@eFpCgVJcAfAwJf@iENuAbAeJRiB`Ew^p@iFpAiM|@_LZuEj@{LReGPkGXcLhAqb@XeKjBms@PaHN}Fj@yStCigA@k@b@iOJmErBew@n@oVZsMD_BRsLFoIBmC@kM?}@EwJM_LYkRGgDa@mVa@sXIqFK}GEcBAc@MuFIkDCg@WgHi@oJu@_NQwDw@qNqA}Uk@cKa@cHQgDm@}KAICi@SuDQeFG_EEkEE}DAaAC{AD}EBoBBmC@c@RuFTgFb@oHhCud@p@kLr@qLh@oIb@sFFw@r@oIbAkKnA_LrBoOzBeO|@mFbGe^bBaKdG}^rAiIhGs_@hAaHpAgHxBgL|DmSxEiVbNos@r@qD\\\\kBTeArAgHfPuz@dMwo@xB{KdEqT~BcMnAkGx@kEvEmV|DaSbB_J^mBhBuKnAyIdBsOl@uHf@}GF{@\\\\uGRsFXyKFuCHkCRaId@gWHcEhAwh@zByhAxAyu@fB_u@tC_vADiBJwEDuANoHDwAhAmj@FcCJcEDwBRoKViLPsH\\\\sNxA_p@PqHBeAjA}g@dAue@RcIFgCXoLVgLB{@Bm@TcJJsEFeBDkABk@ToFHsA^uF`@kFZmE~A_UdGoz@b@aGd@gHfDme@jB}WHcA\\\\_F|AwT`AoM~@gM|AiVLyDN{FD}A@o@@o@BaABeJ@yBEcFAkBKkFC_Bg@gOm@cP[uJKcC{@iU}@qWCg@K}CE}@QgFEaAUwGE_BUsFCaAE}@OkEyDwfAe@qMs@eSoDmbAI_CoEgnAWsHcCgs@{A{a@G{AC]}Bsp@}Awc@GeBASo@kQiCwt@k@gQKmCCyAcAsVsBam@e@}M{A{c@qBcj@UiH]aLImL?kC@yGBmG@iCBkBJiDPyFJyDLuGBg@XkKXgLv@k[RaIx@o[jAkd@Bw@zAml@N{F~@i_@tAkh@FiCD}ADeBHmDJ}DJiEJeEJyD`@cO@S@[DeBDsA`@cMhAHBRDF`ADB{@SAMAc@CGNAPiAIa@bMErAEdBAZARa@bOKxDKdEKhEUfA]|@]f@a@`@e@Pm@NyAOm@Ma@WQQm@q@Sa@I]EWGg@yAp@yE|BuDdB}Ah@eBf@}Cz@m@L_B\\\\{C^aBJeBJeBDgIDyAGuJQmACiFIoBAwP[a\\\\c@eIQiGMo@AmQ[m@CsEImRa@gLU}@A_Pc@wCGwEa@iKqAkDc@yNeBeAOcLsAqDc@mM{Ay]oEs@K{Gy@mIeAoBUsNiB{@Iae@}FeQwBaKqAuHy@mOmBmf@kG}KuA_BSuBWsJkAiVyCyOqBiBUqIcAiH{@aGu@aD]kFm@k@IQCiDc@iC[SCSCaFq@iAMgIkAib@kFs^qEmQyBgEg@kKqA}@Mwf@iGoBW}HaAoG{@ib@eFkEk@cPoBeAMsBWsXkDyt@eJqAOuG{@iGm@mIg@}Ik@aAGiH_@eEUev@eE{F[gTkAqUsA}CQc\\\\kBaZuAi_@qBwVyAyQgAyc@cCkEQo\\\\iBiSkAWAg[}Ag@EeEWeBI_P_AqMq@iX{AkH]c@Cm@AkB?_GBw@?}m@`Ag^d@gX`@on@x@_GDmLNcKNeJNiR\\\\oX\\\\}BB}Yd@eOJuGLqOZwHHaFFoWd@{DDiDD_@@cFFyFFaA?gABoFHcCDcDFgFHWAq@@oFJ_RRwCFac@l@qOTiFHwh@v@iB@yCD{HHcJN_GJy@@gBBeDFO?uOXiGF}ABoBAeQ\\\\iQFwC@iD?mDQuEOeFSuDMqFUsFScHUsCKys@_CSAY?uCCoCFoH\\\\oD^qDj@yw@`MqTbDe@H_RpCq\\\\hFyFx@cBVyDj@mC^m@HoOlCaWzDep@~J{ItAiAPcG~@]Dqe@lHoNxBiEp@gb@tG_O|BsCb@mCb@gIpA]F}HlAgG~@{HjAaInAkBZy@LeFx@sFz@cNvBG@aHdAcBX{QpC_UjDoJzAib@tG_D`@kEv@oDh@_ALsUvDaFt@K@qRzCgDh@qATu@J}MtBqARiBVmDl@aANsBZaBVaANaAJ{@D_ABw@BmB@mAAq@AoDAWAu@?gDCoDAq@AqAAoAAwBA_BAM?}DU}B_@kASkBe@wEuA}CiAs@WgD{AcCsAgBeAe@WsCmB}ByAmByA]WwE_EmEmE}QyPkW_Vu@s@eFuEmAiAUQo@m@TiAHmAEkP?s@Au@@cB@y@@]Do@Be@|Ae\\\\ZsFTsDDa@D]BYN_AJm@Po@Li@Tq@J]N_@N_@P_@P]R[Zc@j@s@rA}AVUvEiFnDgEhAqA|AgB~BkC~BmC~LeNxCiDzAmB|TyX`BuBfDiE|EcGRa@Tu@tAqJ`AcHXqAf@gB{LsWmEmJk@qAWs@U{@iF}ZYkBSkAQeAOkAGm@Em@Ek@Ek@As@?a@?c@@k@@k@FiA\\\\iI|Aka@@Kd@eLBu@Dw@j@uNb@aJTuEnDor@hDmy@vAo\\\\`@iKd@oMhBsa@`Dkw@jCon@VgGD}@HwAzBik@JaCT}EVkGhAwWpAo[@O~@yTv@wRH{BFsB?w@Cw@K}BcAmPi@aJu@gMIuAUqD_A{Oi@yIYqEQqDUuDI{@KkAUeCkBkRcDm\\\\iKqeAgAuKgCcWgAsKQkBCs@Cw@?}@BeBzAal@FyBnBwx@d@cSBc@RgI\\\\uMVqIj@cU\\\\cND}A?Sr@iYh@{Tn@{UDaBP{ChC{Z`@aFLyBDaA@{@HsDXcMtAun@p@u\\\\nCsoA?OjAmh@DgBFaCLoHRuHb@mR\\\\cQZyOL_GR_Jb@sSHkF@a@DkBHqAFgA|BeThBqQZcE^kFlBgZT}DHoBF{BFkCJsFHaFd@s]fAmu@j@mb@PsJNaM@]?e@As@Eo@Gs@Kw@Qs@oDkMg@kBeCuIiIkZgEqNsIwZ_@sA_AcDu@kC_AcDKa@_D}KeGgT}G_VqBeHo@oC]qBeBsLuAwJcCkPCQaAsGGm@YkBE_@Ik@QmA_@iCeB}LyD_XcC{PKu@u@gF}@oGM}@Ga@QoAc@yCWiBWcBa@wCWeBU}AOiAwAyJc@yCiBgMq@yEG]w@yFq@mEwCkS_QalAKq@mEwZOeAsAaJa@qCYwB_@{D_BoQsBkU[cDEi@Ec@KeAi@kGk@}GSsBGk@sC_[o@iH_@_EUcCGk@IcAE_@OaBa@cE[qDa@wEYsCeBmRWmCMkA_@aC[}AWq@}DmKsAkDwDsJyA}Du@iBq@qBe@wAQm@cEgP_Ik[uBkIgBaHoA}EsAkFiAmEc@sAWq@c@{@_AwA_BeC]m@c@_A]cAYaAYwAU}AM}AQwCM{BQwBOkAQaAaEmPMc@cC}Jc@cB[{AW_BYwBIcAGeACiA?qAHqELeI@iBAmAEkAEw@QiCUaCS_BKo@Sw@i@{AoAuD{C{IgIuUeGiQsDsKqKc[qDsKaAqCaAwCiAaDGQcBcFSi@C_@AWBe@FOBS?_@G]OSKGK?g@Sk@[]u@Yk@i@mAu@{As@yAMYQ]Ua@c@}@IKi@gAk@kAWe@We@U]SWg@o@IIc@g@i@e@k@a@g@Wg@YsCuAoBaAqCoAUIeD{AaDuA[MYMkBw@yAu@[MqAi@}Aq@p@iJBWSE}@OaBWKCwEy@}FeAsE}@gNcCiKkBSE[GyAY}Ba@wBa@wB_@mB]c@IoAUc@GiAUuB]_HgAKCkDm@q@Ms@KoB]Co@As@IcCU}GK_BU_CSaA]oAi@uAqF{KwBmEgDgG}D_JgAeCaBiD]w@Sk@[_AU{@WcAUkA]yBGg@MiAKyAGgCEcCIqEOcNGuDSqOEgGEoDCoAGkBCm@QsAc@kDkAaJw@_G[gC_@iCg@{Dg@{DsA{JeDoVcDuVeDmVUoAUqAMu@Os@a@gByDsNyDyNqJq^yAkFAIAC_@wAiBkHm@yCIi@kKkv@mJ}p@aBsLOeAeCsQwDuXkFm`@gPimAc@eDOwAKmAYmEMgBC_B?oCFcDPaHn@uQ|FymA`AgUn@aM^sHNeDTeFVoGz@wSb@qKfB}d@r@eRVoGJ}CJmDr@qTLiPDqH@wA?S@eLAeBm@aOqDi{@c@eKeEicAaH}}AmFie@cAwI{@kHg@kEa@eDIo@wAuL]mCm@mF}Ku`AaHyk@qVusB{Eka@{Dq\\\\QuAQ{AuFue@_^a{CMsDfP{bCnB{YpDmi@DyIOeKSaHiEk_Be@wOiC}hAqConAEcBqEkhB{A{m@EeBiD{sAkD{tAeGscCi@uTEqAk@wKaAgLyCy[u@aIUqCc@eFKeA_C_UcCsWuBqWeDks@oCcJ}Ts|@{HcZsQyeBkHku@aJk{@uJ{}@e@{EuQscBcIit@wGwn@qI_v@UkBiD_\\\\a@qDa@{DuH{r@wP{}AkFqg@[qEIiBEmB\\\\m_@Vq[nAgoAr@ss@D_EFoG`@_a@?E@{AFeGF{F\\\\}[?c@?wAA}@OcDEe@Ic@Qy@aC{I[gASq@cE{NSs@cBiGUw@Ss@o@sBeA{Cq@cBsAyCe@{@kEmGwBiDoBeD{@uA_@m@iDeGuG{K{OwXcEwJkJqTkIuQmJaUcmAclC_KcSyh@qlA{@cBaA_BeA}AwAyAyVeUc\\\\e[iAaA{R_RmBgBca@w_@gk@ci@gNuMqKyJoBgB}AsAwAy@_A[u@WqAWiH_AcBUmMeBam@}HkBa@{Aq@aCyAkTiNyHwF{CgCqBiEcBaFmEgS}Kwh@UcAiBwIgD{Oa@oBwAwGqLol@oJwa@eF}X[{AwBgK_AgDeAeDkC}HwFqPs@}B}G}RgFkOwEoMsLk\\\\qGwSgAkD}HyTaHgScJyW}b@woAsa@ulAyTcp@wh@q}AsE_NkBoFiKq[_M}^gLu\\\\oHmUkAoDgMy^ee@ovA}AqEq\\\\waAaVur@uGiSkI{VqH}T_JqXe@qAi@yA]eAe@}A_@}Ay@aEgEoZka@otCoLuz@mKmt@kRgsAcBsKQqAiAwI_ByJAMKKMAYJwDbI{D~H{@lAoAdAmF|CyOnHaMhF}@\\\\aC~@kGtBwIvCgHtBiIxBsEhAwAZyK`C_Ep@kAR_Fz@_Cf@}LrBkDp@oOtCqM`C_OpCc@HqJ|A_I`BwDb@sMxAwW|BsTpB{UnBsIv@yW~By@FaZnCkBPoFf@oo@~Fk_@dD{CXyMlAw]~CcAHkJx@mE`@cFb@_BNyGn@wCJ}ADw@A}DCeCKmCSwASoCg@oEmAyDwAcFiB}HyCOGyB}@_V}IgE{A}CgAiG{BcDuAqMkFuE{AqB_AuBoAoBwAoAgAqAiA{BkC}A}B_D{FiDeI}@wCwIkYkDgJgB}DoAsBqBkCmFgGwBsBIIqFcFg@o@m@m@qBuBu@{@aAaAa@_@gAeAiA{@y@_@g@Us@Oy@I}@?o@Fo@LUHk@Pq@`@a@^SRc@^YRWLUDUGOMUYg@sAiBoFsBcGy@}Au@wBgBkEcByDmEmJuB}E}DqL{CuLmAgG{@iFaC}NsFy\\\\iC}OeAqGm@kDyBuMc@kCyA_JiAcHeFy[kAmH_EeW{AkIo@uCo@mC}AsGkB}G_CcIuCmIwIoU{BaGkBuEkEsLcDoIa@eA{AeDy@gBc@cAu@yAe@kAmG_PaAsCo@gBk@mBiAkEaA_Eu@eDq@iDq@iEk@mD]kBUoAOgAQoAk@{F_@eEMuAoA{L}@eH_AyFaAaFkAgFaAeEcA_DwAyE}@}BuCiHgPe^aKuT}BeF_E{IyFsMkAoCoJaV}B}GcBsGcAyE{@aFa@kCKu@g@iE_AoKQ_EOiEIaEGuF?kD@gDFoDJaDX}Hf@kLVoGt@wOhEaaAbA}TlBac@l@sM\\\\yBJk@Po@Pc@Ra@`@m@`AwA\\\\c@Z_ANaADqA?_@Ca@Ge@K_@GYK_@MWM[u@cAwB{BqCiDaAqAOe@Ga@Hc@Bg@Gg@M_@QSUGU@SNOXWTe@HsACkBDyALyK|AyNtBgOvBsG|@_RxCeC`@}AReAPqQlC{AT_@Fc@Fg@HwB\\\\wG`AkMjBcB^o@T_@NcC|@yBx@YJiC`Aq@XqC`AoAb@}VhJwNlFyBv@{@ZcIpC_Bp@mAx@_Ax@cBxAeBvBiAhB_@j@sBhDkAhBcA~AeArAy@~@_@\\\\]Z}@t@g@^e@XaElBmEnBwDbB_B`AiBdAgChBo@`@a@XyB`BgEbDUPqAz@{AhAiAz@uI~GWT_E`DeEdDuBdBkBdBgFpFyHpIuG|GaBzAyBjB}D|CoJnHqFdE{EzCsFjD{IlF_B|@y@\\\\y@V{IhBe@HyFdAcBPeA@oAEaDg@iE}@{RuDqAO}AIE?eA@sAHmARMBi@L}@^qAp@oQnKsDxBeBdAi@t@s@jA[j@ELa@`AkEbKqFpMgD`IUj@wDzIcDlHa@x@m@hAkBjC[^Y^yA`Bg@j@[Z_@b@s@t@IH{@|@YZaAhAiChDuBtCeDlFk@z@aArAmAtA}@|@i@d@e@^qA~@iAt@kAh@aAZ}@N_AHcAAy@CaAIy@SwAg@u@]y@e@oA_AaA}@aAgAgA_B_BoCaCqEwK}SwKeTKUmEeIwBaEg@_AyA{Cs@wAiCgF_L{SaKgRs@sAeZgk@eAqBQ[ce@q|@e@}@{CyFsAeCS][g@y@cAkAiAqAcA}DsCgFiDmAy@sCmBwAaAyFsDSMqCgBiD{Bs@m@q@o@e@k@s@}@mAaB_ByBY]_A{Aw@mAm@w@y@}@uAgAcD_BoCsAgBcAIRO^Yn@uE~KkAlCQ^QXUZY\\\\YVi@b@m@d@a@Xu@j@GDiBrAkGdEgJpGKHiSdNcHzEah@z\\\\}CzByApAuAlAsEzEqDhDoF~W[pBMlAIbAIdBMjEWjKyFhsAeKhqB}KvfAwGzo@o@tD}Jp]{Mhe@{ExPaApCy[ln@_@|@Qt@KdA_BtZMpBMlAQz@kDlK[fA_@bBuEdWWnA_@nAwDrKqFdOqBlE_KtS}@dByDpGs@pAm@zAUt@[pAi@nBWr@w@fBW^{B`Di@j@k@b@iL|IfBlSnAxSwBpDQd@Qn@WrBIn@Ah@?l@RxD@~@Cl@Ch@If@Mb@[r@mAhC]z@[fAQfAG|A@tAFjBZdHh@vMf@bNBvACjAK|@wBrHYhAQ`BK|AAxA@tBLhK?~@C~@]~EMfA_@tBc@nBqDnNMt@Kx@[hFKvASjAkJ`Wa@vAe@jBsBnLm@jD_FxReB`GiDfM{D_Ii@iAyAiBeEgFeC}BgAg@wAQkD[[CU@YF}@j@gAd@s@Ls@Bk@Ee@Ia@OaEwBcDaB_NcIkYqQy@o@w@{@e@k@i@}@e@oAk@kBeRhPgKtJg@\\\\i@Nq@D{ACkEOi@Fo@RwPxG}DvA_ALs@FgAKeAWsEqB{@k@}@_AgE{F\"', '2025-12-16 10:18:57'),
(13, 980, 993, 302.68, 228, '\"g`ojIebybD@kByAa@@i@b@WEgBIu@CuBE[BgB?QeDe@KC@qCBuJ?]L{FLuEByA@OHuE@I@g@HaEHyC@{@JwB@QLu@p@aCtAeBjAmAPId@G~Ec@~@G`CWjBM|AGPA~BKdAINEZKZU|@s@pBmBbAcAfIaI`@a@jDgD~B}Bh@i@bAu@RI~@Yl@Id@Ev@@ZHhBj@N`@\\\\TD[F]Fc@lAkJJaAFc@|A}Lh@kEL}@pBmPd@yDNu@Js@Bu@d@yDhA}I`AaIDa@Hk@^{Cx@}GBSfAkIRgAL{@Ju@FmAhAoI|@{IDc@JgAvB_Wx@aJHa@Lg@Nm@FGFMBO@OCSCQAm@?i@?e@zAcQ~@{KLqBFgADqA@qBCcBIoBEm@ScCW_BGk@]yA_@oAc@gA_@{@i@eA{@{AcB{CkA}Bm@kAQc@Qm@Qk@Oo@[qBMaBCm@EkACeA@w@@aAHqBN}BlBk[tAcTRsCRmCR{BRoBb@_En@cENq@Rk@Na@PURMRSL[J_@Fa@Be@Ae@Cc@H}@B_@Dc@TsA|@}DPy@f@wB~CyM|@sDLi@tD_P|AwGrQov@jIc[|BuIJc@Ps@FWz@}DPs@NgBLmADaAFyAV{EbBs[j@eLp@wNbBs]^yHtB_a@X{FZ{FTuENiCLyBBo@PeD`@{I^eIjEqy@tEq~@dCef@~Am\\\\z@uPbAkRp@kPbBe\\\\\\\\uGFkABS?KHmA?MFkADi@PyCJ{AVqCTuBZqBRaAh@qB~@mCz@gBJQLUVa@dHaLzBiDR[bA_BLS^m@vAiCb@aAd@mAbCuIjAmEfFmRr@yBr@{A|@iAvE{EVWlAiAt@_Aj@cATs@Pg@Jo@PgA~AqL^mBd@sAVe@Zi@xEgIP[\\\\k@NSHMBER]FKlIsNdCiE|Vmb@lCuEHObAeB^k@T]\\\\g@PYRYtEuHz@yAVc@zA}CpBcD~AiCj@_Ax@wAh@w@NURSRURSVUPMd@YRIVKTEb@Ex@E`@CVAlAKnAC^@lAJl@J\\\\F`@J|@PZ@Z?hACtFGlBGlAIhBQjAM~Gs@r@GT?zOr@tABhBE\\\\I`@MbB}@~AcAvAs@tAo@dDyApBkAtBoApBaBzFuEnDeDp@{@b@_Aj@yArCiKr@_DfC}JnAgFnBcIXiAnGkVT}@Vu@^eAJ[nEmJ`AoBNWVe@~BuErDgHfIqO`ByCxBgEnBoDvFuLZ}@Z}@fA}C~AqGFWrAsH^uCp@uGZkHTsGZyKHyBDeB@c@Bu@DiBPoGBuD?uTFoNDyBHiBL}APcB`AyJdAgKTiCHgAJ}B?}CAkB?}@AwACyFF}FL}CJuAb@mDhAwFfAyEbCsKvAuGNi@ZkAp@kBrAgCfDsFjb@mr@pLuRlCoEdNmUhFuIrGqKv@oAn@gAnDaGhCeEjDuFdT_^t_@qn@vIiNnAyBl@{@p@{@`BkBjByBBCx@}@pFcGnE{E~AkBbDaExA_DdBqD`@y@hBuDj@qAp@sAtBmEtAwCnLaXrHkPlHkPtEiKr@uA`AeBr@gAn@aALQn@w@l@q@FGx@y@h@i@h@e@f@a@p@i@|@m@zJ{FlI{EdE_ClHgEnF}Cr@c@pAs@LIZOJIh@[j@[j@]rBkARM`@UXO|@k@dAo@XOd@YdAo@XQPK~BwAt@e@XQ|A_AfGsDbQgJjCwA|AcA|AgAlA_AjAcAlAeAlAmAzB_CrSiTnDwDrDwDrBwBpAqA|m@}n@r@s@pAuA|FgG`ImIvD{DnEsEt@w@fFmF~EcF~@aAnX_YfCkC`NcNj@k@lCqCxDyDhC{BvEiDzEaD~J}ENGfBw@vDcBHEfAe@~@c@jD{AnEmB\\\\OfJ_Ezy@g^hAg@VIHEzEsBl@YvCuApAm@nCmA`_@iPbQmHlJ_EdDuAjAe@r@]nCuAZMv@_@t@]x@_@l@WrCoA|M_GjIqD|EcCfB_Ax@e@`C{ArBsAlB}ABA~BcBHE`@KVE`@BZNTVR`@Fr@@n@Cf@Ox@[t@KTy@v@QJIBK?IAKCICGEMMIMGMGOEOCOASAOAk@JmBdAeIbAmIRiBd@aFTeCr@qJZgF^gHPkEb@_Nv@}UpCgaAz@c_@zA_fA\\\\q^HwSDaL\\\\q{ADgQPwxA[i]Q}O?uPB}EBwCRcJPyDb@{JJaDhAsOdByU`AkPZaJTmNA_OYgPe@eMgCmZgHwe@uIkj@aEcZ_BmUo@sOK}GGcGGyHCuDMgFC}EA_KAkCAwD@wMB{JHcJFiFPqJv@c]hAq\\\\v@yQJaBj@uKdBcXdCg[lCk[xC_^TcE^gG\\\\wFb@gHfA}QPeEH{Bd@gMp@sQJaER_GXsGvAgW~@_NJoArAaQrB{SfC{SnC_S~@cGLy@jA}Gx@uEt@kEfEaUjB{JbAiGx@sFr@{Fb@iEDu@NsBLuBTaElAgU`AgOPaC`AqKNuALu@JWJQPKJERAt@Bz@D~CPZBhBP~@Hp@C`BSd@G|@Ab@Bn@JjC`@hAZ~AT`FZ`CRnALtB^lA^|@VrAb@|DdB`Bl@h@P\\\\LzAj@xBp@f@LvCr@hDn@jAPtBb@jDh@bCf@|AVbCb@tDZ~AFdBNnCJtE@jCEvA?tC?nDZrATlBf@zAr@tBdAhCfB`A|@hAhAvBrCv@dAxAvBfA`BvBjDxApBzAjBhAxAbHnHrCvCjJxJ~A`BtH~H~CfDjO`PnE`FnCjCtG~GTT|EdFhExCxAbAdD~AjBr@`AZjBd@bBRbBLtCFlBCvAMz@Kz@MVEh@KjDcAtAc@jAa@t@WlA]dAWv@OhB[vBQ~BEfAAt@Bp@DbAHbDd@bAT`Bb@`Bj@|@`@b@Tj@V`Af@x@h@bAp@bAv@p@n@fIvHfDpCjCnB|@h@vAp@nAd@nBb@z@HdAHzB@dBMB?|AUn@QdAWtAk@|@e@xA{@rAiAhAeA`BkBn@w@hEiF|@w@lAs@z@[r@Or@IdAC|AH|ALRBj@FZB`BLdBBf@?n@Eb@G^Gn@Mv@Ql@Mv@a@rAs@tBaAnCqAb@Mh@K^Eb@Ch@Ah@CxC[NAl@GAq@A_@Ek@mAwGmAeHGe@K}@Ea@Ao@OkJMq@Ag@OkKGqEMiGO}LAs@C}AScLMyFCcBMyJSsMOgK[_VE{BC{BImF_Agl@]mTSoOCaMJ{MJkEJkFVgGPeDVaEb@}FH}@`@mE^mDxAeLx@oFn@mDX{AReA|@mEr@}CbA}Dh@yBbCwJ|DaP~Ie^tBeI\\\\yAlEwQFSn@gCvBuIzB_JpBaIdC{JTcAlAsEdAgElCgLfBaHj[onAxGqWbSkw@dGyUvFuTnA{E`AkD|BgJp@_C|@kCr@kBVk@\\\\o@l@{@d@m@f@g@j@i@z@o@v@a@j@Y~Ak@nA[tCaAj@QfGsBx]wKpLoDrBo@dHuB|HaC`IiCx@WfEoAxE}ArBs@~@a@tAe@fBk@n@SrDeAhBo@lC}@vCy@XKlKyCrDiAvAa@vBm@^KhBk@VI\\\\K`Bi@|X{I|Ae@fTsGzXsIpAa@t@UdK_DnA_@pBm@tE{AdTcGlCu@fFaBrBo@xAe@~EyAhA[hA[tDiAhBm@xBq@hN_EhB_@`C[jDWxCMdH@rBHtBVbBXtAT|ElAdEzA~CxArH~EzFxD`DvBxBtAjInFrB|Al@d@pDhCfCvBbFvEzGxHzAzAvAnAv@r@`C`CxIvIhAhAzDvDh@j@d@v@Rb@FPDVBb@?ZCZGTGRMPMJMJiBlAUHKDMBKEMIIKKSCOCOAQ?UAQ@WDYDWRiAVcA|AeFl@_Bt@kBt@uBjDkJXs@r@{BbA}DjB_F@CjB_F`@gAnBcF`@eA@G|BcGhB{ErAiDDIvLg[zA}Dz@yBDK|BmGFOp@iBFOt@iB@GPe@Zu@tAeDj@{ABI~CeI|@aCvEiMl@aBlDiJbAoC|BiGXs@f@qAVs@pEmL`@cA\\\\{@@CNa@Tk@vVqo@hB{EjLiZz@{BJWnDgJJW^_AXu@t@mBp@iB`AeCrCqHjZow@lHmRzIkUpDaJVo@FQ|CcIfC{GxE{LvAyD~@_CtAiD~@eCjEiLfBwDfB{D~EoKd@eA~C}Gz@gBhDoHn@qAlFsNhDkJvAsD`EuKd@iAfIiTpAiDrCiH|KuYxA{Dr@mBbAoCHQlDgJ~EgMhAgDVs@~@eClA_Dp@eBl@_Bz@}B`@cAjA}CrAmDbAmChAyCNa@d@mAXw@jBwExA}DnGwPlBcFdBeEzG}PdAoBh@y@v@eA|@_Al@g@z@k@z@Yj@UlE{AhOwGv@]bIgDdBs@tOgGdMcF|LcF`Bk@tAg@rA_@jCq@lBc@NCbFi@XCz@GtBGbBCn@A`AAhFIrCIbBG~DKlCM`CKpCQvBMlGk@rCc@tCk@nHkBpJaCdCy@`AW`D}@j@QbCm@l@OnCs@ZGzA_@dFoAnGeBhCw@bC}@fFgCtDyBfCqBlCyBnCiCxI{IvD}DzCwCJMdFgF|SaTlCmCzH}HtAwAJMtGyGNORSn@m@p@o@bCeCtM{MhEiE`TeTdQkQ|F}FTUnAoAzD}DvM_NhIeI`ZcZlEmExD{Df@g@nHoHnNoNzC{CdBeBj@m@LKn@o@n@q@dDcDvBaCLOPSvAoBnAqBtAoCrA{CpAwCxI_S|JgUpEcKTi@jSod@zFqMlBiEnXan@hKyUzD{Ix@iB|CcHrWml@dN_[dCiEhAcBhAaBhAyAnAwAlAuAxAsAbB{Az@k@t@i@dBkA`DyBlAw@|@m@|CoB^U~B}AHGlBuAVONKx@i@bBgAxAaA|@m@RMh@]|FqDrDmCvFqDFEvAgAhAcAvAyBt@iBX_AZaAn@{Cj@iCxAoIj@eDn@gD|A_G`B}FNm@~AqF|@}CrEePrIqZnGaU|Ki`@fQgn@nIiZhE}NhIsXdB_G|CmK|AiFHW|@{ChC{IfPwi@xEcP|EmP~AoFtKi^~@}CTy@lBmGlEiOHYt@gC|CkKjA_EnEkOrCqJl_@apAjTgt@`BwFd@qAzBqGHSj@}AN_@tAmDZs@bCwFp_@uy@hFcL`BoDdAcCpAoC|EiKlMcYrf@gfApZqp@`IeQbBqDdCoF|C{GvIkR|DuI`CkFh@iAHOd@cA~BiFhAcCpCcGj@oAhAcClD{HzWsk@zMuYvPq]fDkFfDoFtCwDpCsDt@eAV_@jBeCj@{@x@kArBsC`DmEzJgNdB{BlFoHdGmIbXu^fLwOHIl^}f@lUuYlCsEdC_Fl@_BzD{KbBoGjBiKlAcK`A_Lb@yLPkMRsQPwO`@a^f@a^P_LbAgt@V}PBqCHuGL}IDiCVqUVcQp@mi@FeFJwJJkGJoG\\\\aKh@aLv@eLn@{H~@_IZeCx@qGfGu]d@gCbCsLvFsYLk@jGsX`B{GvBsIxCgJpCaInBuFtAeETo@d@mAdB{D~@qBlBeErEoJzJcQ|HcLb@k@tDqEzMcPbI{JnFwGHIb@e@LOj@y@~C_EdAcBV]dC_EfCkEzCyFbDcHjEeKvA}DfAiD~BkHd@_BtCmJpJk^zCoLv@wCNi@zCiM\\\\uAh@}Bp@wC|EcSf@sBrKaa@xD_OtBaInFwS~Sax@bXgcAb@}AdBoGXcA^yAvBiI`BuFv@iCrDuIvDgHbFqHjC}C~BoCfAmAtAaBdCsCh@m@TWX_@VY|EsFlDsDpA}AnFgHxEsHtCoEpYsd@pJ{OjGgKh@eApByDrB_E~AuCfSa`@~A{Cr@sAhRi^fIwNxEoGd@q@tCgDrFyFvC{CtEeFtMwN|CsDv@_AFGt@w@HOX_@T[nCoD\\\\c@~FeJhAiBnEcHpFcJlGyLz@kBlEwJlWql@|EwK|C_HbF{JnIsNhDkFrMwOTYjM{MtFkGvBgChDqExFsInF}ItAuC`CcFjFgMhEaLvD{JrBsFvA_EnAaD`FeMh@sArGoPxNg`@dAeD`ImVt@eCvK_b@|CyMd@yBbCkL|AyHrE}WjCqPz@iFD[|A{Ib@{BlDyRhGq^jBwJ`EwP`DuL`DgJ`Ls]dBqFlAyDhBuFj_@yiA~Wiy@|CqJ^gANc@dA_DbAwCjLs]dE_MVy@|@iC|AwEvB_Hn@mBL_@To@v@}Bx@aCzAuE`GsQhPcg@nIiWrDsKBI\\\\_Al@mB|AeFpE{MtCoI``@ejAjFsOhXqx@x@eC~@mCbBiF`BoEj@eB@C`CkHJ]Ri@r@sBvCyI`JiXpLy]dSal@rEuMXy@zBqGdEyLvOid@p`@uiA|DeLdHuSNm@dBcHNu@p@_DfAoFvD}QP}@ZyAvAmHbCeMn@yC|@iElB{IxH{_@t@qDx@gENw@nC_NTkAlAeGxAsH~CaPj@sCbBwIP}@b@_CVkAVoAzIub@l@yCdDgPpDsQrLyl@|Iuc@n@cDr@gD~ByLtBaKvCsNnCuMLm@\\\\cBfCwLHa@FYxJsf@x@wDfI}`@bD{OtHo^hNwq@~CuOrDsQTgAtBaKP}@H]Pw@?ErAmGjCkM`@qBrDsQj@oCvFwXlBeJxCsNt@qDbM_n@vBkKJi@DUJe@FW`CqLb@}BZ}AxCeOhSqbAlAcGdCwLjFkW~@oEvDeRvB_KdA_FbEoR\\\\mAdNqg@vBaIj@sBBKV}@HYXeAbHkWxN}h@v@yC~BqIj@qBzEkQ`Qyn@fP{l@dKa_@|]cpAtKg`@lNsf@v@oC^uAjAkEXgAvE{PjNyg@dC_JbAuDbLya@bEaOXeAtA{EnCiJLg@|DePn@qCx@{DjAgGjAoGzB_NlCwNtAsGpKah@pCiNvBeKpC_NbCoLv@yD\\\\cBl@uCjCiMzKmi@zE{UfDqPj@oC`@iBzCsN\\\\aBrKqh@vQs|@hEcUlTuhAt@uDf@gCZwAxAiHBKp@_DhAkFLq@f@iCr@kDLi@DWXwANw@xEiUjCsMh@_CxAsGbAyDvAkF~@gDfAwDz@_DzBqIbCeKn@uC|C{Nh@gCrBaKfBeJr@gDlCoM|AuHtDuQ~@eFl@yDp@{Ej@qFf@sIHoBLcEDoEBgB@mFEsFG}B]gJQkDGiA[kDa@kD_@{Cq@kE_BuIaAiEoBaH}AqEu@wBgBkEcByDmEmJuB}E}DqL{CuLmAgG{@iFaC}NsFy\\\\iC}OeAqGm@kDyBuMc@kCyA_JiAcHeFy[kAmH_EeW{AkIo@uCo@mC}AsGkB}G_CcIuCmIwIoU{BaGkBuEkEsLcDoIa@eA{AeDy@gBc@cAu@yAe@kAmG_PaAsCo@gBk@mBiAkEaA_Eu@eDq@iDq@iEk@mD]kBUoAOgAQoAk@{F_@eEMuAoA{L}@eH_AyFaAaFkAgFaAeEcA_DwAyE}@}BuCiHgPe^aKuT}BeF_E{IyFsMkAoCoJaV}B}GcBsGcAyE{@aFa@kCKu@g@iE_AoKQ_EOiEIaEGuF?kD@gDFoDJaDX}Hf@kLVoGt@wOhEaaAbA}TlBac@l@sM`AgT@GjBob@^uHXgH`@iIt@gPPwEHeBZaHFgAf@yLDgANgDD_AHoBp@qPrB_c@zB_f@d@kKd@wKvA{[pBge@\\\\_Gp@eLj@oHv@kHt@uFh@gDVaBbAqF|A_H~@wDfAsDjA{DnByFpLiZ`AcCvRwf@`Om_@tX{r@|IwTpG{OlC{GdDqGjCsEhC{D|C}DfDmD`B{ADEhC_CrGqErE{BbAg@bGwBd@OpGyBhHeCfBm@hDkA`FcBzG}BvPaGfIuCjC}@nJeDrG{BnL}DfA_@zIyCvFkB~\\\\cLdF_BlGeBjCs@fE}A`@OfFgBpKqDbJeDzE_BfFeBfF_BdCu@hCo@xDaAbAOrGoArEk@`@GfE[rAE|AKhACxCIdB?|A?vB?zABzXRnDF|@?bIHx@@lFBhBC~AIjCUhAMbAQjOoCpIuAzFaAjASrNgCfDk@jCc@dCe@fCe@lAUdCa@bHiAxCi@pFaAbSoDzJwA~Ci@`F{@bB[bCWrAIjACrB?|DPzDj@hDz@zAj@xAh@rB`AvBpATLJHdHvEvCpB`BdA|CrBbF`DtZdSpCfBdAr@bKtG`DnBfCpAfEbBdF~A|Bd@|ATlBT|ANdADdABzAH`D@v@@hCKlCUvAQjB]xD_AbM}DvDgBxD{BjEyCx@s@xBmBlCqCnAqAn@s@`C{CfCoDfCaE`CoE~B}EjAwCjHkSpPqe@`AoCf@sAL]zCuIl@aBHWb@kA`@kATq@x@_C~EsNj@cBvLa]fTom@hOeb@pNu`@rEmMxBiGf@uAXu@rC}HFSt@sBp@kBdB_FbGqPnSck@xCiIbOua@dCoHxBkIvBmKl@eDl@mDh@wD~@_I`@eEZcExBe]`@oGPkCFy@Du@B[JaBDw@xDcp@z@uNNkCBa@Du@PmCv@qNr@aMp@iLd@}GLiCNkDL}DJmEF_FAkG?yE?gB?]CiBCyBEcBImCKwCEiAOyEIqD?iB@}CBkAHkEBs@LgCPqBJkABWHq@PyAb@aD\\\\oBNaAPcApBqL\\\\sBfEaW|CcRbBwKl@kEf@eGZcEZqE`@}FHwAB]JuAp@_JFiAFaA~@_OdA_QHoAh@iHR{CJgBb@kG|@}MvGsbAx@yL@OT}C`@}Gb@mGh@kI~AuUt@cLB[@SJyAB[L{B^oF@MHwApBaZl@gJ`@mGf@eHbAePDm@z@_MjAaQz@oMt@uL^wFVsEJoDFeE?eC@aACeDAeAGgCKwDq@_MGiASeDKyEG_G@cDD{CTmHVyGTgIDqD?{BAmBMcFSaDm@sG}@uKS}EGsDAoDBaC\\\\yZHuHDkC\\\\oYHyGJqHHeGJ}DDuAj@eLPuCb@eGJ{AfAwOpCg_@`@iFf@gEpA_JdBgKHg@d@wCh@mDlAwJFy@b@}Ez@oMxBob@^}Gv@}NFkAvAuXd@mGf@uFr@wGf@uDh@mDx@yETeAb@}BvAkGzA_GrB}G~E{NxCaJ|@mCz@gCjBiGp@iC\\\\mAdAmEjAcGjAuGhAwH~@cHpAmJnAyI~@mFrAgGTcAh@yBVgAjBsG`@yAb@}AV}@jDuLlIwZ|DaNjE_OtIeZbB{FdAoDjCaJdCqIbAgDhEiOpBmHrAaErAyGl@}Bh@qBzCgK~@cDjGmTfFoQh@wBvAaFdDsKbAiDpAiFdAiFZyAXoA`EgT~DcT~I_f@nA{GnAqG~AqIrFkYnCcMfDcOrEeShAaFd@{BjCkLTcAFYViAdFyTpB{I~EoTx@kDtBsJx@qDn@sCtB{IPu@`@oBfCaLh@aC`BoHbG}WzFcWtFqVzFaWd@wBbBoH~H}]jCiLxCwMb@mBfAyEfCaL~AcH~CgNz@cEnAqFf@yBh@yB~AaHdAwEdCyKhCqKdJu]bA{DxJi_@tFcTfHiXx@yCfB_HpWubAv[anAlEyPjUm|@jEsPnOml@~DqOjLwc@ZkAdDcMLm@`BmGlEwPhHiXdAuDpBoGbBcGjBeHnGgVzJk_@\\\\oA~Mwh@l@yBXiA|AeGvFcUrAyFnB_JlBwIpBcJdAmEpAsFRy@fCqKtJwb@jFwVJg@zDcQdAaFxCaNl@wC`@uBl@cE`@yC^cENgCJkBNaEHgD?}@?kCAqDK}DKuCGaAMqBIgAO}AKgAUkBi@wDgBgKq@_Ek@yDe@sDg@aEYaDWeDScCKgBW{FE{@AeA@c@DWN_@JQNKFCP?L@PPLPJVVr@Z|AJv@TpALt@BZ?XAP?XBTDTHRJLJHLDN?LEJIJMHQXs@X{@P]PWXYTSLIDC^U\\\\IdEkAlA[l@QXGrA_@vBm@pBi@NE\\\\IfFsAJClCs@ZIHCbFsA`L}CPE~N{DhUeG~Ac@NEzKuCxBm@lHmBpBi@f@Mn@Q~@UfCs@RCdI{B`D}@tA]LEbAWfCm@|@UxBk@d@MjA[p@QZIzC}@dBa@RGnA[xBeAh@]JIHIHm@j@aLVoDvCJxDFH{C@k@\"', '2025-12-16 10:39:29');

-- --------------------------------------------------------

--
-- Структура таблицы `carriers`
--

CREATE TABLE `carriers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `color` varchar(7) NOT NULL,
  `max_weight` decimal(6,2) NOT NULL,
  `base_cost` decimal(8,2) NOT NULL,
  `cost_per_kg` decimal(8,3) NOT NULL,
  `cost_per_km` decimal(8,4) NOT NULL,
  `speed_kmh` decimal(6,2) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `carriers`
--

INSERT INTO `carriers` (`id`, `name`, `color`, `max_weight`, `base_cost`, `cost_per_kg`, `cost_per_km`, `speed_kmh`, `description`) VALUES
(1, 'Белпочта', '#d32f2f', 30.00, 4.50, 0.250, 0.0080, 60.00, 'Государственная почта'),
(2, 'DPD', '#0066cc', 30.00, 9.00, 0.800, 0.0180, 95.00, 'Международная доставка'),
(3, 'СДЭК (CDEK)', '#ff9800', 20.00, 8.50, 0.700, 0.0200, 90.00, 'Курьерская служба'),
(4, 'Европочта', '#4caf50', 25.00, 6.50, 0.500, 0.0120, 80.00, 'Пункты выдачи'),
(6, 'Autolight Express', '#e91e63', 30.00, 9.50, 0.750, 0.0190, 92.00, 'Быстрая доставка');

-- --------------------------------------------------------

--
-- Структура таблицы `courier_stats`
--

CREATE TABLE `courier_stats` (
  `id` int(11) NOT NULL,
  `courier_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `orders_delivered` int(11) DEFAULT 0,
  `distance_covered` decimal(8,2) DEFAULT 0.00,
  `revenue_generated` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `offices`
--

CREATE TABLE `offices` (
  `id` int(11) NOT NULL,
  `carrier_id` int(11) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `offices`
--

INSERT INTO `offices` (`id`, `carrier_id`, `city`, `address`, `lat`, `lng`) VALUES
(807, 1, 'Минск', 'пр-т Независимости, 10', 53.90040000, 27.56120000),
(808, 1, 'Минск', 'пр-т Дзержинского, 91', 53.85920000, 27.48500000),
(809, 1, 'Минск', 'ул. Уманская, 55', 53.87600000, 27.49870000),
(810, 1, 'Гродно', 'ул. Гая, 11', 53.68180000, 23.83670000),
(811, 1, 'Каменец', 'ул. Брестская, 38', 52.40240000, 23.81510000),
(812, 1, 'Высокое', 'ул. Ленина, 1', 52.37000000, 23.37000000),
(813, 1, 'Гродно', 'ул. Гагарина, 45', 53.65420000, 23.84790000),
(814, 1, 'Минск', 'ул. Янки Мавра, 33а', 53.90160000, 27.47210000),
(815, 1, 'Минск', 'пр-т Партизанский, 12', 53.87650000, 27.62680000),
(816, 1, 'Гомель', 'ул. Советская, 21', 52.43540000, 30.97540000),
(817, 1, 'Брест', 'ул. Советская, 46', 52.09740000, 23.68800000),
(818, 1, 'Гродно', 'ул. Ожешко, 1', 53.68800000, 23.83400000),
(819, 1, 'Витебск', 'ул. Ленина, 20', 55.19040000, 30.20490000),
(820, 1, 'Могилёв', 'ул. Первомайская, 42', 53.90000000, 30.33690000),
(821, 1, 'Борисов', 'ул. 3 Интернационала, 15', 54.22800000, 28.50500000),
(822, 1, 'Барановичи', 'ул. Советская, 89', 53.13270000, 26.01370000),
(823, 1, 'Бобруйск', 'ул. Советская, 95', 53.14490000, 29.22460000),
(824, 1, 'Мозырь', 'ул. Ленинская, 10', 52.04950000, 29.26560000),
(825, 1, 'Солигорск', 'пр-т Мира, 15', 52.78980000, 27.53570000),
(826, 1, 'Пинск', 'ул. Гагарина, 22', 52.11530000, 26.10310000),
(827, 1, 'Лида', 'ул. Советская, 33', 53.88720000, 25.29970000),
(828, 1, 'Слоним', 'ул. Октябрьская, 8', 53.09490000, 25.32270000),
(829, 1, 'Новополоцк', 'ул. Карвата, 5', 55.53200000, 28.65840000),
(830, 1, 'Орша', 'ул. Интернациональная, 45', 54.51080000, 30.42540000),
(831, 1, 'Молодечно', 'ул. Советская, 67', 54.31350000, 26.83900000),
(832, 1, 'Слуцк', 'ул. Ленина, 18', 53.02740000, 27.55970000),
(833, 1, 'Дзержинск', 'ул. Победы, 25', 53.68320000, 27.13800000),
(834, 1, 'Марьина Горка', 'ул. Советская, 5', 53.51090000, 28.14700000),
(835, 1, 'Столбцы', 'ул. Советская, 15', 53.48230000, 26.74330000),
(836, 1, 'Кобрин', 'ул. Ленина, 48', 52.21380000, 24.35640000),
(837, 1, 'Иваново', 'ул. Советская, 30', 52.14510000, 25.53240000),
(838, 1, 'Каменец', 'ул. Ленина, 10', 52.40090000, 23.81960000),
(839, 1, 'Старые Дороги', 'ул. Советская, 8', 53.04020000, 28.26700000),
(840, 1, 'Климовичи', 'ул. Ленина, 5', 53.61220000, 31.95860000),
(841, 1, 'Речица', 'ул. Советская, 15', 52.36390000, 30.39470000),
(842, 1, 'Светлогорск', 'ул. Мира, 30', 52.63290000, 29.73400000),
(843, 1, 'Рогачёв', 'ул. Ленина, 25', 53.08010000, 30.05050000),
(844, 1, 'Жлобин', 'ул. Советская, 85', 52.89260000, 30.02400000),
(845, 1, 'Калинковичи', 'ул. Ленина, 20', 52.13230000, 29.32570000),
(846, 1, 'Корма', 'ул. Советская, 10', 53.13000000, 30.80160000),
(847, 1, 'Чаусы', 'ул. Ленина, 10', 53.80760000, 30.97060000),
(848, 1, 'Быхов', 'ул. Ленина, 15', 53.52110000, 30.24590000),
(849, 1, 'Кричев', 'ул. Ленина, 25', 53.71910000, 31.71390000),
(850, 1, 'Глуск', 'ул. Советская, 10', 52.90290000, 28.68420000),
(851, 1, 'Круглое', 'ул. Ленина, 15', 54.24970000, 29.79680000),
(852, 1, 'Кличев', 'ул. Советская, 5', 53.49250000, 29.05560000),
(853, 1, 'Чериков', 'ул. Ленина, 10', 53.56500000, 32.57210000),
(854, 1, 'Белыничи', 'ул. Советская, 15', 53.99830000, 29.70910000),
(855, 1, 'Кировск', 'ул. Ленина, 10', 53.27010000, 29.47520000),
(856, 1, 'Шклов', 'ул. Советская, 20', 54.21380000, 30.28660000),
(857, 1, 'Костюковичи', 'ул. Ленина, 10', 53.35390000, 32.05150000),
(858, 1, 'Горки', 'ул. Советская, 15', 54.28610000, 30.98620000),
(859, 1, 'Осиповичи', 'ул. Советская, 10', 53.30580000, 28.63860000),
(860, 1, 'Бобруйск', 'ул. Гагарина, 50', 53.16520000, 29.18750000),
(861, 1, 'Быхов', 'ул. Мира, 25', 53.52110000, 30.24590000),
(862, 1, 'Глуск', 'ул. Победы, 15', 52.90290000, 28.68420000),
(863, 1, 'Дрибин', 'ул. Ленина, 10', 54.11670000, 31.10000000),
(864, 1, 'Климовичи', 'ул. Победы, 20', 53.61220000, 31.95860000),
(865, 1, 'Краснополье', 'ул. Советская, 15', 53.33560000, 31.39980000),
(866, 1, 'Круглое', 'ул. Мира, 10', 54.24970000, 29.79680000),
(867, 1, 'Кличев', 'ул. Победы, 15', 53.49250000, 29.05560000),
(868, 1, 'Кричев', 'ул. Мира, 20', 53.71910000, 31.71390000),
(869, 1, 'Корма', 'ул. Мира, 12', 53.13000000, 30.80160000),
(870, 1, 'Мстиславль', 'ул. Советская, 20', 54.01840000, 31.72170000),
(871, 1, 'Чаусы', 'ул. Мира, 15', 53.80760000, 30.97060000),
(872, 1, 'Чериков', 'ул. Мира, 12', 53.56500000, 32.57210000),
(873, 1, 'Шумилино', 'ул. Ленина, 10', 55.30190000, 29.61610000),
(874, 1, 'Поставы', 'ул. Советская, 15', 55.11680000, 26.83930000),
(875, 1, 'Сенно', 'ул. Ленина, 15', 54.81360000, 29.70490000),
(876, 1, 'Толочин', 'ул. Советская, 10', 54.40940000, 29.69630000),
(877, 1, 'Ушачи', 'ул. Советская, 5', 55.17900000, 28.61580000),
(878, 1, 'Чашники', 'ул. Ленина, 10', 54.85840000, 29.16080000),
(879, 1, 'Шарковщина', 'ул. Советская, 10', 55.36890000, 27.46850000),
(880, 1, 'Лепель', 'ул. Ленина, 15', 54.88500000, 28.69910000),
(881, 1, 'Браслав', 'ул. Советская, 15', 55.64110000, 27.04170000),
(882, 1, 'Верхнедвинск', 'ул. Ленина, 10', 55.81670000, 27.93330000),
(883, 1, 'Городок', 'ул. Советская, 10', 55.46240000, 30.99250000),
(884, 1, 'Дятлово', 'ул. Ленина, 15', 53.46310000, 25.40610000),
(885, 1, 'Зельва', 'ул. Советская, 5', 53.15040000, 24.81430000),
(886, 1, 'Ивье', 'ул. Ленина, 10', 53.92970000, 25.34020000),
(887, 1, 'Кореличи', 'ул. Советская, 15', 53.56980000, 26.14100000),
(888, 1, 'Мосты', 'ул. Советская, 20', 53.41220000, 24.53870000),
(889, 1, 'Новогрудок', 'ул. Ленина, 25', 53.59710000, 25.82510000),
(890, 1, 'Островец', 'ул. Советская, 10', 54.61480000, 25.95810000),
(891, 1, 'Ошмяны', 'ул. Ленина, 15', 54.42450000, 25.93640000),
(892, 1, 'Свислочь', 'ул. Советская, 10', 53.44840000, 24.09860000),
(893, 1, 'Скидель', 'ул. Советская, 15', 53.59040000, 24.24770000),
(894, 1, 'Волковыск', 'ул. Советская, 25', 53.15620000, 24.45130000),
(895, 1, 'Гродно', 'ул. Берестовицкая, 10', 53.67280000, 23.81380000),
(896, 1, 'Дятлово', 'ул. Мира, 20', 53.46310000, 25.40610000),
(897, 1, 'Зельва', 'ул. Мира, 12', 53.15040000, 24.81430000),
(898, 2, 'Минск', 'ул. Липковская, 9/3, офис 35', 53.90600000, 27.65600000),
(899, 2, 'Минск', 'ул. Сурганова, 57б, офис 175', 53.92600000, 27.58600000),
(900, 2, 'Минск', 'пр-т Независимости, 6', 53.89600000, 27.54700000),
(901, 2, 'Минск', 'ул. Русиянана, 36', 53.93500000, 27.66800000),
(902, 2, 'Минск', 'ул. Веры Хоружей, 6Б', 53.91200000, 27.57600000),
(903, 2, 'Брест', 'ул. Московская, 208', 52.10400000, 23.77000000),
(904, 2, 'Витебск', 'ул. Ленина, 10', 55.19000000, 30.20500000),
(905, 2, 'Гомель', 'ул. Советская, 10', 52.43450000, 30.97540000),
(906, 2, 'Гродно', 'ул. Ожешко, 4', 53.68840000, 23.83450000),
(907, 2, 'Могилёв', 'ул. Ленинская, 14', 53.90000000, 30.33100000),
(908, 2, 'Барановичи', 'ул. Ленина, 12', 53.13270000, 26.01370000),
(909, 2, 'Бобруйск', 'ул. Интернациональная, 22', 53.14490000, 29.22460000),
(910, 2, 'Полоцк', 'ул. Октябрьская, 25', 55.48500000, 28.76860000),
(911, 2, 'Минск', 'ул. Притыцкого, 29', 53.90690000, 27.48470000),
(912, 2, 'Минск', 'ул. Немига, 5', 53.90550000, 27.55040000),
(913, 2, 'Минск', 'ул. Карвата, 84', 53.88500000, 27.67400000),
(914, 2, 'Гомель', 'пр-т Ленина, 10', 52.42480000, 30.99720000),
(915, 2, 'Брест', 'ул. Гоголя, 15', 52.09260000, 23.69260000),
(916, 2, 'Гродно', 'ул. Горького, 50', 53.67630000, 23.84030000),
(917, 2, 'Витебск', 'ул. Карла Маркса, 1', 55.18800000, 30.20800000),
(918, 2, 'Могилёв', 'ул. Ленинская, 4', 53.89730000, 30.33140000),
(919, 2, 'Борисов', 'ул. 3 Интернационала, 15', 54.22800000, 28.50500000),
(920, 2, 'Барановичи', 'ул. Советская, 89', 53.13270000, 26.01370000),
(921, 2, 'Бобруйск', 'ул. Советская, 95', 53.14490000, 29.22460000),
(922, 2, 'Мозырь', 'ул. Гагарина, 30', 52.04950000, 29.26560000),
(923, 2, 'Солигорск', 'пр-т Независимости, 56', 52.78980000, 27.53570000),
(924, 2, 'Пинск', 'ул. Космонавтов, 12', 52.11530000, 26.10310000),
(925, 2, 'Лида', 'ул. Октябрьская, 45', 53.88720000, 25.29970000),
(926, 2, 'Слоним', 'ул. Октябрьская, 8', 53.09490000, 25.32270000),
(927, 2, 'Новополоцк', 'ул. Мира, 78', 55.53200000, 28.65840000),
(928, 2, 'Орша', 'ул. Ленинская, 34', 54.51080000, 30.42540000),
(929, 2, 'Молодечно', 'ул. Центральная, 19', 54.31350000, 26.83900000),
(930, 2, 'Слуцк', 'ул. Ленина, 18', 53.02740000, 27.55970000),
(931, 2, 'Дзержинск', 'ул. Победы, 25', 53.68320000, 27.13800000),
(932, 2, 'Марьина Горка', 'ул. Советская, 5', 53.51090000, 28.14700000),
(933, 2, 'Столбцы', 'ул. Советская, 15', 53.48230000, 26.74330000),
(934, 2, 'Кобрин', 'ул. Ленина, 48', 52.21380000, 24.35640000),
(935, 2, 'Иваново', 'ул. Советская, 30', 52.14510000, 25.53240000),
(936, 2, 'Каменец', 'ул. Ленина, 10', 52.40090000, 23.81960000),
(937, 2, 'Старые Дороги', 'ул. Советская, 8', 53.04020000, 28.26700000),
(938, 2, 'Климовичи', 'ул. Ленина, 5', 53.61220000, 31.95860000),
(939, 2, 'Речица', 'ул. Советская, 15', 52.36390000, 30.39470000),
(940, 2, 'Светлогорск', 'ул. Мира, 30', 52.63290000, 29.73400000),
(941, 2, 'Рогачёв', 'ул. Ленина, 25', 53.08010000, 30.05050000),
(942, 2, 'Жлобин', 'ул. Советская, 85', 52.89260000, 30.02400000),
(943, 2, 'Калинковичи', 'ул. Ленина, 20', 52.13230000, 29.32570000),
(944, 2, 'Корма', 'ул. Советская, 10', 53.13000000, 30.80160000),
(945, 2, 'Чаусы', 'ул. Ленина, 10', 53.80760000, 30.97060000),
(946, 2, 'Быхов', 'ул. Ленина, 15', 53.52110000, 30.24590000),
(947, 2, 'Кричев', 'ул. Ленина, 25', 53.71910000, 31.71390000),
(948, 2, 'Глуск', 'ул. Советская, 10', 52.90290000, 28.68420000),
(949, 2, 'Круглое', 'ул. Ленина, 15', 54.24970000, 29.79680000),
(950, 2, 'Кличев', 'ул. Советская, 5', 53.49250000, 29.05560000),
(951, 2, 'Чериков', 'ул. Ленина, 10', 53.56500000, 32.57210000),
(952, 2, 'Белыничи', 'ул. Советская, 15', 53.99830000, 29.70910000),
(953, 2, 'Кировск', 'ул. Ленина, 10', 53.27010000, 29.47520000),
(954, 2, 'Шклов', 'ул. Советская, 20', 54.21380000, 30.28660000),
(955, 2, 'Костюковичи', 'ул. Ленина, 10', 53.35390000, 32.05150000),
(956, 2, 'Горки', 'ул. Советская, 15', 54.28610000, 30.98620000),
(957, 2, 'Осиповичи', 'ул. Советская, 10', 53.30580000, 28.63860000),
(958, 3, 'Минск', 'ул. Сухаревская, 70', 53.90200000, 27.49600000),
(959, 3, 'Минск', 'ул. Уборевича, 88', 53.84200000, 27.63200000),
(960, 3, 'Минск', 'ул. Центральная, 6', 53.90200000, 27.54500000),
(961, 3, 'Минск', 'ул. Народная, 31', 53.87500000, 27.60100000),
(962, 3, 'Минск', 'ул. Франциска Скорины, 5', 53.93400000, 27.64800000),
(963, 3, 'Минск', 'ул. Уманская, 55', 53.87600000, 27.49870000),
(964, 3, 'Минск', 'пр-т Дзержинского, 11', 53.90700000, 27.50200000),
(965, 3, 'Гомель', 'ул. Советская, 21', 52.43540000, 30.97540000),
(966, 3, 'Брест', 'ул. Советская, 46', 52.09740000, 23.68800000),
(967, 3, 'Гродно', 'ул. Ожешко, 1', 53.68800000, 23.83400000),
(968, 3, 'Витебск', 'ул. Ленина, 20', 55.19040000, 30.20490000),
(969, 3, 'Могилёв', 'ул. Первомайская, 42', 53.90000000, 30.33690000),
(970, 3, 'Борисов', 'ул. 3 Интернационала, 15', 54.22800000, 28.50500000),
(971, 3, 'Барановичи', 'ул. Советская, 89', 53.13270000, 26.01370000),
(972, 3, 'Бобруйск', 'ул. Советская, 95', 53.14490000, 29.22460000),
(973, 3, 'Мозырь', 'ул. Ленинская, 10', 52.04950000, 29.26560000),
(974, 3, 'Солигорск', 'ул. Молодёжная, 89', 52.78980000, 27.53570000),
(975, 3, 'Пинск', 'ул. Кирова, 23', 52.11530000, 26.10310000),
(976, 3, 'Лида', 'ул. Советская, 33', 53.88720000, 25.29970000),
(977, 3, 'Слоним', 'ул. Октябрьская, 8', 53.09490000, 25.32270000),
(978, 3, 'Новополоцк', 'ул. Брестская, 44', 55.53200000, 28.65840000),
(979, 3, 'Орша', 'ул. Карвата, 12', 54.51080000, 30.42540000),
(980, 3, 'Молодечно', 'ул. Советская, 67', 54.31350000, 26.83900000),
(981, 3, 'Слуцк', 'ул. Ленина, 18', 53.02740000, 27.55970000),
(982, 3, 'Дзержинск', 'ул. Победы, 25', 53.68320000, 27.13800000),
(983, 3, 'Марьина Горка', 'ул. Советская, 5', 53.51090000, 28.14700000),
(984, 3, 'Столбцы', 'ул. Советская, 15', 53.48230000, 26.74330000),
(985, 3, 'Кобрин', 'ул. Ленина, 48', 52.21380000, 24.35640000),
(986, 3, 'Иваново', 'ул. Советская, 30', 52.14510000, 25.53240000),
(987, 3, 'Каменец', 'ул. Ленина, 10', 52.40090000, 23.81960000),
(988, 3, 'Старые Дороги', 'ул. Советская, 8', 53.04020000, 28.26700000),
(989, 3, 'Климовичи', 'ул. Ленина, 5', 53.61220000, 31.95860000),
(990, 3, 'Речица', 'ул. Советская, 15', 52.36390000, 30.39470000),
(991, 3, 'Светлогорск', 'ул. Мира, 30', 52.63290000, 29.73400000),
(992, 3, 'Рогачёв', 'ул. Ленина, 25', 53.08010000, 30.05050000),
(993, 3, 'Жлобин', 'ул. Советская, 85', 52.89260000, 30.02400000),
(994, 3, 'Калинковичи', 'ул. Ленина, 20', 52.13230000, 29.32570000),
(995, 3, 'Корма', 'ул. Советская, 10', 53.13000000, 30.80160000),
(996, 3, 'Чаусы', 'ул. Ленина, 10', 53.80760000, 30.97060000),
(997, 3, 'Быхов', 'ул. Ленина, 15', 53.52110000, 30.24590000),
(998, 3, 'Кричев', 'ул. Ленина, 25', 53.71910000, 31.71390000),
(999, 3, 'Глуск', 'ул. Советская, 10', 52.90290000, 28.68420000),
(1000, 3, 'Круглое', 'ул. Ленина, 15', 54.24970000, 29.79680000),
(1001, 3, 'Кличев', 'ул. Советская, 5', 53.49250000, 29.05560000),
(1002, 3, 'Чериков', 'ул. Ленина, 10', 53.56500000, 32.57210000),
(1003, 3, 'Белыничи', 'ул. Советская, 15', 53.99830000, 29.70910000),
(1004, 3, 'Кировск', 'ул. Ленина, 10', 53.27010000, 29.47520000),
(1005, 3, 'Шклов', 'ул. Советская, 20', 54.21380000, 30.28660000),
(1006, 3, 'Костюковичи', 'ул. Ленина, 10', 53.35390000, 32.05150000),
(1007, 3, 'Горки', 'ул. Советская, 15', 54.28610000, 30.98620000),
(1008, 3, 'Осиповичи', 'ул. Советская, 10', 53.30580000, 28.63860000),
(1009, 4, 'Минск', 'ул. Кульман, 9', 53.91530000, 27.57740000),
(1010, 4, 'Минск', 'пл. Победы', 53.90870000, 27.57470000),
(1011, 4, 'Минск', 'ул. Сурганова, 57', 53.92600000, 27.58600000),
(1012, 4, 'Минск', 'ул. Одоевского, 117', 53.89900000, 27.47400000),
(1013, 4, 'Минск', 'ул. Притыцкого, 156', 53.90800000, 27.43000000),
(1014, 4, 'Гродно', 'ул. Поповича, 5', 53.67900000, 23.82900000),
(1015, 4, 'Брест', 'ул. Московская, 202', 52.08500000, 23.70000000),
(1016, 4, 'Гомель', 'ул. Ильича, 33', 52.42000000, 30.97000000),
(1017, 4, 'Витебск', 'ул. Карла Маркса, 1', 55.18800000, 30.20800000),
(1018, 4, 'Могилёв', 'пр-т Мира, 21', 53.90200000, 30.33900000),
(1019, 4, 'Борисов', 'ул. 3 Интернационала, 15', 54.22800000, 28.50500000),
(1020, 4, 'Барановичи', 'ул. Советская, 89', 53.13270000, 26.01370000),
(1021, 4, 'Бобруйск', 'ул. Советская, 95', 53.14490000, 29.22460000),
(1022, 4, 'Мозырь', 'ул. Партизанская, 15', 52.04950000, 29.26560000),
(1023, 4, 'Солигорск', 'ул. Космонавтов, 14', 52.78980000, 27.53570000),
(1024, 4, 'Пинск', 'ул. Строителей, 33', 52.11530000, 26.10310000),
(1025, 4, 'Лида', 'ул. Маяковского, 7', 53.88720000, 25.29970000),
(1026, 4, 'Слоним', 'ул. Октябрьская, 8', 53.09490000, 25.32270000),
(1027, 4, 'Новополоцк', 'ул. Октябрьская, 41', 55.53200000, 28.65840000),
(1028, 4, 'Орша', 'ул. Свободы, 29', 54.51080000, 30.42540000),
(1029, 4, 'Молодечно', 'ул. Ленина, 37', 54.31350000, 26.83900000),
(1030, 4, 'Слуцк', 'ул. Ленина, 18', 53.02740000, 27.55970000),
(1031, 4, 'Дзержинск', 'ул. Победы, 25', 53.68320000, 27.13800000),
(1032, 4, 'Марьина Горка', 'ул. Советская, 5', 53.51090000, 28.14700000),
(1033, 4, 'Столбцы', 'ул. Советская, 15', 53.48230000, 26.74330000),
(1034, 4, 'Кобрин', 'ул. Ленина, 48', 52.21380000, 24.35640000),
(1035, 4, 'Иваново', 'ул. Советская, 30', 52.14510000, 25.53240000),
(1036, 4, 'Каменец', 'ул. Ленина, 10', 52.40090000, 23.81960000),
(1037, 4, 'Старые Дороги', 'ул. Советская, 8', 53.04020000, 28.26700000),
(1038, 4, 'Климовичи', 'ул. Ленина, 5', 53.61220000, 31.95860000),
(1039, 4, 'Речица', 'ул. Советская, 15', 52.36390000, 30.39470000),
(1040, 4, 'Светлогорск', 'ул. Мира, 30', 52.63290000, 29.73400000),
(1041, 4, 'Рогачёв', 'ул. Ленина, 25', 53.08010000, 30.05050000),
(1042, 4, 'Жлобин', 'ул. Советская, 85', 52.89260000, 30.02400000),
(1043, 4, 'Калинковичи', 'ул. Ленина, 20', 52.13230000, 29.32570000),
(1044, 4, 'Корма', 'ул. Советская, 10', 53.13000000, 30.80160000),
(1045, 4, 'Чаусы', 'ул. Ленина, 10', 53.80760000, 30.97060000),
(1046, 4, 'Быхов', 'ул. Ленина, 15', 53.52110000, 30.24590000),
(1047, 4, 'Кричев', 'ул. Ленина, 25', 53.71910000, 31.71390000),
(1048, 4, 'Глуск', 'ул. Советская, 10', 52.90290000, 28.68420000),
(1049, 4, 'Круглое', 'ул. Ленина, 15', 54.24970000, 29.79680000),
(1050, 4, 'Кличев', 'ул. Советская, 5', 53.49250000, 29.05560000),
(1051, 4, 'Чериков', 'ул. Ленина, 10', 53.56500000, 32.57210000),
(1052, 4, 'Белыничи', 'ул. Советская, 15', 53.99830000, 29.70910000),
(1053, 4, 'Кировск', 'ул. Ленина, 10', 53.27010000, 29.47520000),
(1054, 4, 'Шклов', 'ул. Советская, 20', 54.21380000, 30.28660000),
(1055, 4, 'Костюковичи', 'ул. Ленина, 10', 53.35390000, 32.05150000),
(1056, 4, 'Горки', 'ул. Советская, 15', 54.28610000, 30.98620000),
(1057, 4, 'Осиповичи', 'ул. Советская, 10', 53.30580000, 28.63860000),
(1107, 6, 'Минск', 'ул. Одоевского, 117', 53.89900000, 27.47400000),
(1108, 6, 'Брест', 'ул. Московская, 208', 52.10400000, 23.77000000),
(1109, 6, 'Минск', 'пр-т Рокоссовского, 1А', 53.85900000, 27.47400000),
(1110, 6, 'Минск', 'пр-т Партизанский, 12', 53.87650000, 27.62680000),
(1111, 6, 'Гомель', 'ул. Крестьянская, 12', 52.43450000, 30.97540000),
(1112, 6, 'Брест', 'ул. 17 Сентября, 10', 52.09740000, 23.68800000),
(1113, 6, 'Гродно', 'ул. Горького, 50', 53.67630000, 23.84030000),
(1114, 6, 'Витебск', 'ул. Карла Маркса, 1', 55.18800000, 30.20800000),
(1115, 6, 'Могилёв', 'ул. Ленинская, 4', 53.89730000, 30.33140000),
(1116, 6, 'Борисов', 'ул. 3 Интернационала, 15', 54.22800000, 28.50500000),
(1117, 6, 'Барановичи', 'ул. Советская, 89', 53.13270000, 26.01370000),
(1118, 6, 'Бобруйск', 'ул. Советская, 95', 53.14490000, 29.22460000),
(1119, 6, 'Мозырь', 'ул. Октябрьская, 12', 52.04950000, 29.26560000),
(1120, 6, 'Солигорск', 'ул. Мира, 89', 52.78980000, 27.53570000),
(1121, 6, 'Пинск', 'ул. Маяковского, 45', 52.11530000, 26.10310000),
(1122, 6, 'Лида', 'ул. Ленина, 78', 53.88720000, 25.29970000),
(1123, 6, 'Слоним', 'ул. Октябрьская, 8', 53.09490000, 25.32270000),
(1124, 6, 'Новополоцк', 'ул. Карвата, 34', 55.53200000, 28.65840000),
(1125, 6, 'Орша', 'ул. Космонавтов, 56', 54.51080000, 30.42540000),
(1126, 6, 'Молодечно', 'ул. Свободы, 23', 54.31350000, 26.83900000),
(1127, 6, 'Слуцк', 'ул. Ленина, 18', 53.02740000, 27.55970000),
(1128, 6, 'Дзержинск', 'ул. Победы, 25', 53.68320000, 27.13800000),
(1129, 6, 'Марьина Горка', 'ул. Советская, 5', 53.51090000, 28.14700000),
(1130, 6, 'Столбцы', 'ул. Советская, 15', 53.48230000, 26.74330000),
(1131, 6, 'Кобрин', 'ул. Ленина, 48', 52.21380000, 24.35640000),
(1132, 6, 'Иваново', 'ул. Советская, 30', 52.14510000, 25.53240000),
(1133, 6, 'Каменец', 'ул. Ленина, 10', 52.40090000, 23.81960000),
(1134, 6, 'Старые Дороги', 'ул. Советская, 8', 53.04020000, 28.26700000),
(1135, 6, 'Климовичи', 'ул. Ленина, 5', 53.61220000, 31.95860000),
(1136, 6, 'Речица', 'ул. Советская, 15', 52.36390000, 30.39470000),
(1137, 6, 'Светлогорск', 'ул. Мира, 30', 52.63290000, 29.73400000),
(1138, 6, 'Рогачёв', 'ул. Ленина, 25', 53.08010000, 30.05050000),
(1139, 6, 'Жлобин', 'ул. Советская, 85', 52.89260000, 30.02400000),
(1140, 6, 'Калинковичи', 'ул. Ленина, 20', 52.13230000, 29.32570000),
(1141, 6, 'Корма', 'ул. Советская, 10', 53.13000000, 30.80160000),
(1142, 6, 'Чаусы', 'ул. Ленина, 10', 53.80760000, 30.97060000),
(1143, 6, 'Быхов', 'ул. Ленина, 15', 53.52110000, 30.24590000),
(1144, 6, 'Кричев', 'ул. Ленина, 25', 53.71910000, 31.71390000),
(1145, 6, 'Глуск', 'ул. Советская, 10', 52.90290000, 28.68420000),
(1146, 6, 'Круглое', 'ул. Ленина, 15', 54.24970000, 29.79680000),
(1147, 6, 'Кличев', 'ул. Советская, 5', 53.49250000, 29.05560000),
(1148, 6, 'Чериков', 'ул. Ленина, 10', 53.56500000, 32.57210000),
(1149, 6, 'Белыничи', 'ул. Советская, 15', 53.99830000, 29.70910000),
(1150, 6, 'Кировск', 'ул. Ленина, 10', 53.27010000, 29.47520000),
(1151, 6, 'Шклов', 'ул. Советская, 20', 54.21380000, 30.28660000),
(1152, 6, 'Костюковичи', 'ул. Ленина, 10', 53.35390000, 32.05150000),
(1153, 6, 'Горки', 'ул. Советская, 15', 54.28610000, 30.98620000),
(1154, 6, 'Осиповичи', 'ул. Советская, 10', 53.30580000, 28.63860000);

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `carrier_id` int(11) DEFAULT NULL,
  `from_office` int(11) DEFAULT NULL,
  `to_office` int(11) DEFAULT NULL,
  `weight` decimal(8,3) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `delivery_hours` decimal(8,2) DEFAULT NULL,
  `track_number` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `payment_status` varchar(20) DEFAULT 'pending',
  `tracking_status` varchar(50) DEFAULT 'created',
  `full_name` varchar(255) DEFAULT NULL,
  `home_address` text DEFAULT NULL,
  `pickup_city` varchar(100) DEFAULT NULL,
  `pickup_address` text DEFAULT NULL,
  `delivery_city` varchar(100) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `desired_date` date DEFAULT NULL,
  `insurance` tinyint(1) DEFAULT 0,
  `packaging` tinyint(1) DEFAULT 0,
  `fragile` tinyint(1) DEFAULT 0,
  `payment_method` varchar(50) DEFAULT 'cash',
  `comment` text DEFAULT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `recipient_address` text DEFAULT NULL,
  `courier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `carrier_id`, `from_office`, `to_office`, `weight`, `cost`, `delivery_hours`, `track_number`, `created_at`, `payment_status`, `tracking_status`, `full_name`, `home_address`, `pickup_city`, `pickup_address`, `delivery_city`, `delivery_address`, `desired_date`, `insurance`, `packaging`, `fragile`, `payment_method`, `comment`, `recipient_name`, `recipient_address`, `courier_id`) VALUES
(1, 2, 4, NULL, NULL, 30.000, 49.28, 1.60, '7493FC43F2BC', '2025-12-04 03:43:01', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(2, 1, 1, NULL, NULL, 1.000, 8.60, 8.00, '3E93445DFB40', '2025-12-10 21:10:10', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(3, 1, 2, NULL, NULL, 1.000, 19.12, 5.50, '14DA538AF5EC', '2025-12-10 21:10:14', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(4, 1, 2, NULL, NULL, 1.000, 19.12, 5.50, '614C04987B6D', '2025-12-10 21:22:43', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(5, 1, 2, NULL, NULL, 1.000, 19.12, 5.50, '793FA52FC4C1', '2025-12-10 21:22:45', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(6, 2, 3, NULL, NULL, 20.000, 34.68, 6.40, 'BE1E0039F0E1', '2025-12-10 23:06:46', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(7, 2, NULL, NULL, NULL, 15.000, 37.69, 8.70, '28BD4368DF28', '2025-12-10 23:07:14', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(8, 1, 2, NULL, NULL, 25.000, 34.89, 3.40, 'F3F6FDC39031', '2025-12-10 23:37:25', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(9, 1, NULL, NULL, NULL, 1.000, 16.12, 3.90, 'A05B842C6F32', '2025-12-10 23:37:30', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(10, 1, 1, NULL, NULL, 20.000, 13.45, 8.20, '5C570435A40B', '2025-12-10 23:45:07', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(11, 1, 2, NULL, NULL, 14.000, 27.85, 4.50, '74AF25CBE2BB', '2025-12-10 23:56:26', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(12, 1, NULL, NULL, NULL, 1.000, 18.10, 4.90, 'F08FBD9B5A40', '2025-12-10 23:56:32', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(13, 1, NULL, NULL, NULL, 1.000, 8.90, NULL, '697BF70D4F5E', '2025-12-11 00:21:05', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(14, 1, 2, NULL, NULL, 1.000, 15.69, 3.40, '5C12CA9A2444', '2025-12-11 00:28:32', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(15, 2, 3, NULL, NULL, 1.000, 9.20, NULL, 'D0EF1CFA0C3D', '2025-12-11 00:51:10', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(16, 1, 1, NULL, NULL, 1.000, 4.75, NULL, '85294E7B1F26', '2025-12-11 01:36:06', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(17, 1, 3, NULL, NULL, 20.000, 27.50, NULL, '1B8D3E650FC3', '2025-12-11 01:43:38', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(18, 1, NULL, NULL, NULL, 1.000, 8.90, NULL, 'E52192163F55', '2025-12-11 01:47:19', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(19, 1, 1, NULL, NULL, 1.000, 4.75, NULL, '72EB923A6942', '2025-12-11 01:49:08', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(20, 1, 1, NULL, NULL, 1.000, 9.75, NULL, '282F6ADD9436', '2025-12-11 01:51:53', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(21, 1, 1, NULL, NULL, 1.000, 8.70, NULL, '4143234C5D6F', '2025-12-11 02:24:26', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(22, 1, 6, NULL, NULL, 1.000, 10.25, NULL, 'B6500BD7BE6E', '2025-12-11 03:05:41', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(23, 1, 2, NULL, NULL, 1.000, 9.80, NULL, 'E65372640861', '2025-12-11 03:06:10', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(24, 1, NULL, NULL, NULL, 1.000, 8.90, NULL, 'C9CA5F4BDE8F', '2025-12-11 03:09:28', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(25, 1, 6, NULL, NULL, 1.000, 10.25, NULL, 'E066182601F0', '2025-12-11 03:12:57', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(26, 1, 6, NULL, NULL, 1.000, 10.25, NULL, 'FFBA88B5EC8C', '2025-12-11 03:20:51', 'pending', 'created', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cash', NULL, NULL, NULL, NULL),
(27, 1, 6, NULL, NULL, 1.000, 10.25, NULL, '8D308F9FAFC0', '2025-12-11 03:23:49', 'pending', 'delayed', 'Журко Александр Сергеевич', 'у3у33у3у', NULL, NULL, NULL, NULL, '0000-00-00', 0, 0, 0, 'card', '', NULL, NULL, NULL),
(28, 1, 1, NULL, NULL, 1.000, 9.87, NULL, 'B21DD1FBD3B9', '2025-12-11 03:40:21', 'paid', 'delayed', 'Журко Александр Сергеевич', '2ууууууц', NULL, NULL, NULL, NULL, '2025-12-27', 0, 0, 0, 'card', '', NULL, NULL, NULL),
(29, 1, 1, NULL, NULL, 1.000, 9.07, NULL, '2378BFE432F1', '2025-12-11 04:08:41', 'paid', 'in_transit', 'Журко Александр Сергеевич', 'ккккукуу', NULL, NULL, NULL, NULL, '0000-00-00', 0, 0, 0, 'cash', '', NULL, NULL, NULL),
(30, 1, NULL, NULL, NULL, 1.000, 14.04, NULL, '4500771A939B', '2025-12-11 16:29:34', 'paid', 'delivered', 'Журко Александр Сергеевич', 'к3к3к', NULL, NULL, NULL, NULL, '0000-00-00', 0, 1, 1, 'cash', '', NULL, NULL, NULL),
(31, 1, NULL, NULL, NULL, 1.000, 14.22, NULL, '999BE30355BF', '2025-12-11 17:52:46', 'paid', 'processed', 'Журко Александр Сергеевич', 'епеееп', NULL, NULL, NULL, NULL, '2025-12-27', 1, 1, 1, 'card', '', NULL, NULL, NULL),
(32, 1, 2, NULL, NULL, 25.000, 60.00, NULL, '3688F351906A', '2025-12-11 18:13:35', 'paid', 'processed', 'Журко Александр Сергеевич', 'ррпрпр', NULL, NULL, NULL, NULL, '2025-12-26', 1, 1, 0, 'card', '', NULL, NULL, NULL),
(33, 1, 1, NULL, NULL, 15.000, 11.11, NULL, 'E1703B14B348', '2025-12-11 18:15:59', 'paid', 'processed', 'Журко Александр Сергеевич', '6666', NULL, NULL, NULL, NULL, '0000-00-00', 0, 0, 0, 'cash', '', NULL, NULL, NULL),
(34, 1, NULL, NULL, NULL, 15.000, 21.93, NULL, 'AE35BCEEF03D', '2025-12-11 18:26:58', 'paid', 'out_for_delivery', 'Журко Александр Сергеевич', 'вувувув', NULL, NULL, NULL, NULL, '2025-12-13', 1, 0, 0, 'card', '', NULL, NULL, NULL),
(35, 1, 6, NULL, NULL, 25.000, 33.82, NULL, '0C3B97658374', '2025-12-11 19:14:07', 'paid', 'processed', 'Журко Александр Сергеевич', 'fffefef', NULL, NULL, NULL, NULL, '2025-12-13', 1, 1, 0, 'card', 'оставить у двери', NULL, NULL, NULL),
(36, 1, 1, NULL, NULL, 15.000, 7.37, NULL, 'F6995BB62470', '2025-12-11 20:01:39', 'paid', 'processed', 'Журко Александр Сергеевич', 'trrgr', NULL, NULL, NULL, NULL, '2025-12-14', 0, 0, 0, 'card', '', NULL, NULL, NULL),
(37, 1, 1, NULL, NULL, 15.000, 11.88, NULL, 'C0EA3227C447', '2025-12-12 12:27:44', 'paid', 'cancelled', 'Журко Александр Сергеевич', 'grgr', NULL, NULL, NULL, NULL, '2025-12-17', 1, 1, 1, 'card', 'оставить у двери', NULL, NULL, NULL),
(38, 2, 6, NULL, NULL, 15.000, 26.43, NULL, '07B52F4D89B0', '2025-12-12 12:53:21', 'paid', 'returned', 'Журко Александр Сергеевич', 'к3к3к', NULL, NULL, NULL, NULL, '2025-12-26', 1, 1, 1, 'cash', 'у2у2у2у', NULL, NULL, NULL),
(39, 1, 6, NULL, NULL, 15.000, 26.43, NULL, 'B1B9C1114E7B', '2025-12-12 13:59:57', 'pending', 'created', 'Журко Александр Сергеевич', '444к', NULL, NULL, NULL, NULL, '2025-12-19', 1, 1, 1, 'cash', 'к3к3к3', NULL, NULL, NULL),
(40, 1, 6, NULL, NULL, 15.000, 26.43, NULL, 'AF72317BA15A', '2025-12-12 14:00:10', 'pending', 'created', 'Журко Александр Сергеевич', '444к', NULL, NULL, NULL, NULL, '2025-12-19', 1, 1, 1, 'cash', 'к3к3к3', NULL, NULL, NULL),
(41, 1, 6, NULL, NULL, 15.000, 26.43, NULL, '039DD009F679', '2025-12-12 14:02:38', 'pending', 'created', 'Журко Александр Сергеевич', '444к', NULL, NULL, NULL, NULL, '2025-12-19', 1, 1, 1, 'cash', 'к3к3к3', NULL, NULL, NULL),
(42, 1, 1, NULL, NULL, 15.000, 12.34, NULL, 'EA3526982A1B', '2025-12-12 14:03:09', 'pending', 'created', 'Журко Александр Сергеевич', '44r4r', NULL, NULL, NULL, NULL, '2025-12-19', 1, 1, 1, 'cash', 'dwertyuil', NULL, NULL, NULL),
(43, 1, 6, NULL, NULL, 14.000, 25.65, NULL, 'B5C9A194AF6E', '2025-12-12 14:09:23', 'paid', 'delivered', 'Журко Александр Сергеевич', 'фывапро', NULL, NULL, NULL, NULL, '2025-12-19', 1, 1, 1, 'cash', 'фывапро', NULL, NULL, NULL),
(44, 1, 1, NULL, NULL, 15.000, 8.15, NULL, '4E7EA5569B19', '2025-12-12 14:27:30', 'paid', 'paid', 'Журко Александр Сергеевич', 'ывапролд', NULL, NULL, NULL, NULL, '2025-12-17', 1, 1, 1, 'cash', 'вапролд', NULL, NULL, NULL),
(45, 1, 6, NULL, NULL, 15.000, 24.41, NULL, 'DF943F68B429', '2025-12-12 14:43:33', 'paid', 'paid', 'Журко Александр Сергеевич', 'укенг', NULL, NULL, NULL, NULL, '2025-12-26', 1, 1, 1, 'cash', 'вапролбрпав', NULL, NULL, NULL),
(46, 1, 1, NULL, NULL, 15.000, 12.45, NULL, '35FF980A97F3', '2025-12-12 15:20:54', 'paid', 'delayed', 'Журко Александр Сергеевич', 'вапро', NULL, NULL, NULL, NULL, '2025-12-18', 1, 1, 1, 'cash', 'ваенгшщ', NULL, NULL, NULL),
(47, 1, 6, NULL, NULL, 15.000, 24.41, NULL, '92D7F49D4DC4', '2025-12-12 18:13:21', 'paid', 'delivered', 'Журко Александр Сергеевич', '3456', NULL, NULL, NULL, NULL, '2025-12-18', 1, 1, 1, 'cash', '3456', NULL, NULL, NULL),
(48, 1, 1, NULL, NULL, 1.000, 8.65, NULL, 'F6BBF0B976B1', '2025-12-12 19:54:45', 'paid', 'paid', 'Журко Александр Сергеевич', 'll', NULL, NULL, NULL, NULL, '2025-12-18', 1, 1, 1, 'cash', '234rth', NULL, NULL, NULL),
(49, 1, 1, 826, 894, 15.000, 12.85, NULL, '441CAD3F5EB6', '2025-12-14 20:54:27', 'paid', 'paid', 'Журко Александр Сергеевич', 'итю.', NULL, NULL, NULL, NULL, '0000-00-00', 1, 1, 1, 'cash', 'енлроп', NULL, NULL, NULL),
(50, 1, 2, 914, 903, 14.000, 32.98, NULL, '39933F61B34D', '2025-12-14 21:03:13', 'paid', 'paid', 'Журко Александр Сергеевич', 'ц3456', NULL, NULL, NULL, NULL, '2025-12-17', 1, 1, 1, 'cash', 'цу4к5е6ш', NULL, NULL, NULL),
(51, 1, 1, 807, 894, 15.000, 13.36, NULL, '3E0E4FFBF90B', '2025-12-14 21:20:30', 'paid', 'paid', 'Журко Александр Сергеевич', '2345678', NULL, NULL, NULL, NULL, '0000-00-00', 1, 1, 1, 'cash', '', NULL, NULL, NULL),
(52, 1, 6, 1133, 1154, 15.000, 31.04, NULL, '2B9DEEEDF184', '2025-12-14 21:28:06', 'paid', 'paid', 'Журко Александр Сергеевич', 'цукенг', NULL, NULL, NULL, NULL, '2025-12-18', 1, 1, 1, 'cash', 'укенгшвапмс', NULL, NULL, NULL),
(53, 1, 1, 807, 894, 15.000, 13.36, NULL, '7C1FDE0BEEF1', '2025-12-14 21:45:02', 'paid', 'paid', 'Журко Александр Сергеевич', 'ролдж', NULL, NULL, NULL, NULL, '2025-12-16', 1, 1, 1, 'cash', 'ывапенгшлорпавывапр', NULL, NULL, NULL),
(54, 1, 3, 960, 986, 16.000, 28.21, NULL, 'CAF1A40ED1D0', '2025-12-14 21:53:15', 'paid', 'paid', 'Журко Александр Сергеевич', 'лдждлоро', NULL, NULL, NULL, NULL, '0000-00-00', 1, 1, 1, 'cash', 'олдлорпр', NULL, NULL, NULL),
(55, 1, 1, 826, 828, 16.000, 12.78, NULL, '3D77B1CAD2FA', '2025-12-15 10:16:04', 'paid', 'paid', 'Журко Александр Сергеевич', 'фывапрол', NULL, NULL, NULL, NULL, '2025-12-18', 1, 1, 1, 'cash', 'ывапролд', NULL, NULL, NULL),
(56, 1, 6, 1153, 1140, 14.000, 28.80, NULL, '73B6537160DA', '2025-12-15 11:05:47', 'paid', 'paid', 'Журко Александр Сергеевич', '3456789', NULL, NULL, NULL, NULL, '2025-12-27', 1, 1, 1, 'cash', '23456789twqq5', NULL, NULL, NULL),
(57, 1, 2, 948, 942, 15.000, 22.62, NULL, '7501B0159712', '2025-12-15 11:16:23', 'paid', 'cancelled', 'Журко Александр Сергеевич', 'у4к5е67', NULL, NULL, NULL, NULL, '0000-00-00', 0, 0, 0, 'cash', '', NULL, NULL, NULL),
(58, 1, 1, 826, 894, 13.000, 12.34, NULL, 'B43926FD550D', '2025-12-16 12:45:48', 'paid', 'paid', 'Журко Александр Сергеевич', 'Советская 37', NULL, NULL, NULL, NULL, '2025-12-18', 1, 1, 1, 'cash', 'Оставить у двери', NULL, NULL, NULL),
(59, 1, 3, 826, 1001, 13.000, 28.48, NULL, '6CAA1242137D', '2025-12-16 13:20:07', 'paid', 'delivered', 'Журко Александр Сергеевич', 'Советская 37 Пинск', NULL, NULL, NULL, NULL, '2025-12-17', 1, 1, 1, 'cash', 'Оставить у двери', 'Зенькович Илья Александрович', 'Кличев Ломоносова 12', NULL),
(60, 1, 6, 1140, 1128, 1.000, 14.55, NULL, 'AFE11FDCDA2E', '2025-12-16 15:39:52', 'paid', 'paid', 'Журко Александр Сергеевич', 'ждзх', NULL, NULL, NULL, NULL, '0000-00-00', 0, 0, 0, 'cash', '', 'rgrgrgrg', 'долдж', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `tracking_status_history`
--

CREATE TABLE `tracking_status_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `tracking_status_history`
--

INSERT INTO `tracking_status_history` (`id`, `order_id`, `status`, `description`, `created_at`) VALUES
(1, 28, 'processed', 'Заказ оплачен и обработан', '2025-12-11 00:40:39'),
(2, 29, 'processed', 'Заказ оплачен и обработан', '2025-12-11 01:08:44'),
(3, 30, 'processed', 'Заказ оплачен и обработан', '2025-12-11 13:30:16'),
(4, 31, 'processed', 'Заказ оплачен и обработан', '2025-12-11 14:52:57'),
(5, 32, 'processed', 'Заказ оплачен и обработан', '2025-12-11 15:13:45'),
(6, 33, 'processed', 'Заказ оплачен и обработан', '2025-12-11 15:16:02'),
(7, 34, 'processed', 'Заказ оплачен и обработан', '2025-12-11 15:28:02'),
(8, 35, 'processed', 'Заказ оплачен и обработан', '2025-12-11 16:14:24'),
(9, 36, 'processed', 'Заказ оплачен и обработан', '2025-12-11 17:02:01'),
(10, 37, 'processed', 'Заказ оплачен и обработан', '2025-12-12 09:28:06'),
(11, 38, 'processed', 'Заказ оплачен и обработан', '2025-12-12 09:53:25'),
(12, 41, 'created', 'Заказ создан', '2025-12-12 11:02:38'),
(13, 42, 'created', 'Заказ создан', '2025-12-12 11:03:09'),
(14, 43, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-12 11:09:23'),
(15, 43, 'paid', 'Заказ оплачен и обработан', '2025-12-12 11:09:39'),
(16, 44, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-12 11:27:30'),
(17, 44, 'paid', 'Заказ оплачен и обработан', '2025-12-12 11:27:34'),
(18, 45, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-12 11:43:33'),
(19, 45, 'paid', 'Заказ оплачен и обработан', '2025-12-12 11:43:35'),
(20, 46, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-12 12:20:55'),
(21, 46, 'paid', 'Заказ оплачен и обработан', '2025-12-12 12:21:03'),
(22, 46, 'delayed', 'Статус изменен с \'paid\' на \'delayed\'', '2025-12-12 12:23:05'),
(23, 47, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-12 15:13:21'),
(24, 47, 'paid', 'Заказ оплачен и обработан', '2025-12-12 15:13:24'),
(25, 47, 'sort_center', 'Статус изменен с \'paid\' на \'sort_center\'', '2025-12-12 16:29:23'),
(26, 47, 'delayed', 'Статус изменен с \'sort_center\' на \'delayed\'', '2025-12-12 16:53:44'),
(27, 47, 'cancelled', 'Статус изменен с \'delayed\' на \'cancelled\'', '2025-12-12 16:53:52'),
(28, 48, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-12 16:54:45'),
(29, 48, 'paid', 'Заказ оплачен и обработан', '2025-12-12 16:54:48'),
(30, 49, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-14 17:54:27'),
(31, 49, 'paid', 'Заказ оплачен и обработан', '2025-12-14 17:54:35'),
(32, 50, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-14 18:03:13'),
(33, 50, 'paid', 'Заказ оплачен и обработан', '2025-12-14 18:03:16'),
(34, 51, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-14 18:20:30'),
(35, 51, 'paid', 'Заказ оплачен и обработан', '2025-12-14 18:20:34'),
(36, 52, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-14 18:28:06'),
(37, 52, 'paid', 'Заказ оплачен и обработан', '2025-12-14 18:28:21'),
(38, 53, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-14 18:45:02'),
(39, 53, 'paid', 'Заказ оплачен и обработан', '2025-12-14 18:45:06'),
(40, 54, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-14 18:53:15'),
(41, 54, 'paid', 'Заказ оплачен и обработан', '2025-12-14 18:53:18'),
(42, 55, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-15 07:16:04'),
(43, 55, 'paid', 'Заказ оплачен и обработан', '2025-12-15 07:16:07'),
(44, 56, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-15 08:05:47'),
(45, 56, 'paid', 'Заказ оплачен и обработан', '2025-12-15 08:05:54'),
(46, 57, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-15 08:16:23'),
(47, 57, 'paid', 'Заказ оплачен и обработан', '2025-12-15 08:16:25'),
(48, 57, 'cancelled', 'Статус изменен с \'paid\' на \'cancelled\'', '2025-12-16 09:44:20'),
(49, 58, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-16 09:45:49'),
(50, 58, 'paid', 'Заказ оплачен и обработан', '2025-12-16 09:46:21'),
(51, 59, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-16 10:20:07'),
(52, 59, 'paid', 'Заказ оплачен и обработан', '2025-12-16 10:20:13'),
(53, 60, 'pending', 'Заказ создан, ожидает оплаты', '2025-12-16 12:39:52'),
(54, 60, 'paid', 'Заказ оплачен и обработан', '2025-12-16 12:39:56');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin','courier') DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `name`, `email`, `phone`, `role`, `created_at`) VALUES
(1, 'admin', 'admin', 'Администратор', NULL, NULL, 'admin', '2025-12-04 03:19:52'),
(2, 'Aliaksandr', '123456', 'Александр', 'szhurko005@gmail.com', NULL, 'user', '2025-12-04 03:41:51'),
(3, 'Курьер1', '123456', 'Даниил', 'kourier1@gmail.com', NULL, 'courier', '2025-12-16 15:37:59');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `calculated_routes`
--
ALTER TABLE `calculated_routes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_route` (`from_office_id`,`to_office_id`),
  ADD KEY `to_office_id` (`to_office_id`);

--
-- Индексы таблицы `carriers`
--
ALTER TABLE `carriers`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `courier_stats`
--
ALTER TABLE `courier_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courier_date` (`courier_id`,`date`);

--
-- Индексы таблицы `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carrier_id` (`carrier_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `track_number` (`track_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `carrier_id` (`carrier_id`),
  ADD KEY `from_office` (`from_office`),
  ADD KEY `to_office` (`to_office`),
  ADD KEY `courier_id` (`courier_id`);

--
-- Индексы таблицы `tracking_status_history`
--
ALTER TABLE `tracking_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `calculated_routes`
--
ALTER TABLE `calculated_routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `carriers`
--
ALTER TABLE `carriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `courier_stats`
--
ALTER TABLE `courier_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `offices`
--
ALTER TABLE `offices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1156;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT для таблицы `tracking_status_history`
--
ALTER TABLE `tracking_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `calculated_routes`
--
ALTER TABLE `calculated_routes`
  ADD CONSTRAINT `calculated_routes_ibfk_1` FOREIGN KEY (`from_office_id`) REFERENCES `offices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `calculated_routes_ibfk_2` FOREIGN KEY (`to_office_id`) REFERENCES `offices` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `courier_stats`
--
ALTER TABLE `courier_stats`
  ADD CONSTRAINT `courier_stats_ibfk_1` FOREIGN KEY (`courier_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `offices`
--
ALTER TABLE `offices`
  ADD CONSTRAINT `offices_ibfk_1` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`from_office`) REFERENCES `offices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_4` FOREIGN KEY (`to_office`) REFERENCES `offices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_5` FOREIGN KEY (`courier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `tracking_status_history`
--
ALTER TABLE `tracking_status_history`
  ADD CONSTRAINT `tracking_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

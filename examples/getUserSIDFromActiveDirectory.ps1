$strComputer = "."

$objWMIService = GetObject("winmgmts:\\$($strComputer)\root\cimv2")

$objAccount = $objWMIService.Get _ ("Win32_UserAccount.Name='a_nahaje00',Domain='grpinextenso.dom'")

Wscript.Echo $objAccount.SID
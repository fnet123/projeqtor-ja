Attribute VB_Name = "Module1"
Option Explicit
Dim nbFiles As Integer
Dim nbVars As Integer

Sub generateLangFiles()

    On Error GoTo 0
    
    Dim col As Integer
    Dim folder As String
    
    col = 3
    nbFiles = 0
    
    'Sort before saving
    Cells.Select
    Selection.Sort Key1:=Range("A2"), Order1:=xlAscending, Header:=xlGuess, _
        OrderCustom:=1, MatchCase:=False, Orientation:=xlTopToBottom, _
        DataOption1:=xlSortNormal
        
    Call checkDupplicate






    folder = ChooseDirectory
    
    If folder <> "OperationCanceled" Then
        While Cells(1, col) <> ""
            Call writeFile(folder, Cells(1, col), col)
            nbFiles = nbFiles + 1
            col = col + 1
        Wend
   
        MsgBox "Completed" & _
        vbCrLf & "  Number of files generated : " & nbFiles & _
        vbCrLf & "  Number of lignes per file : " & nbVars & _
        vbCrLf & "  Saved in " & CurDir
    End If
      
    
    
End Sub


Function ChooseDirectory() As String
Dim fd As FileDialog
Set fd = Application.FileDialog(msoFileDialogFolderPicker)
'get the number of the button chosen
Dim FolderChosen As Integer
FolderChosen = fd.Show

If FolderChosen <> -1 Then
'didn't choose anything (clicked on CANCEL)
MsgBox "Canceled"
ChooseDirectory = "OperationCanceled"
Else
'display name and path of file chosen
ChooseDirectory = fd.SelectedItems(1)
End If
End Function

Sub writeFile(ByVal folder As String, lang As String, col As Integer)

    Dim lig As Integer
    Dim filename As String
    Dim val As String
    Dim ligne As String
    Dim fd As FileDialog
    
    If lang = "default" Then
        filename = folder & "/lang.js"
    Else

    folder = folder & "/" & lang
        If Dir(folder, vbDirectory) = "" Then

            MkDir folder
        End If
        filename = folder & "/lang.js"
    End If
    
    If Dir(filename) <> "" Then
        Kill filename
    End If
    
    'Open filename For Output As #1
    Dim fsT
    Set fsT = CreateObject("ADODB.Stream")
    fsT.Type = 2
    fsT.Charset = "utf-8"
    fsT.Open
    
    'Print #1, "{"
    fsT.writeText "{" & vbCrLf
    
    lig = 2
    While Cells(lig, 1) <> ""
        val = Cells(lig, col)
        If val = "" Then
            val = "[" & Cells(lig, 3) & "]"
        End If
        'val = Encode_UTF8(val, lang)
        ligne = Cells(lig, 1) & ": """ & val & ""","
        'MsgBox ligne
        'Print #1, ligne
        fsT.writeText ligne & vbCrLf
        lig = lig + 1
    Wend
    
    nbVars = lig - 2
    
    'Print #1, "currentLocaleOfFile: """ & lang & """"
    fsT.writeText "currentLocaleOfFile: """ & lang & """" & vbCrLf
    'Print #1, "}"
    fsT.writeText "}" & vbCrLf
    'Close #1
    fsT.SaveToFile filename, 2
    
End Sub


Public Function Encode_UTF8(astr, lang)
    Dim c
    Dim n
    Dim utftext
     
    utftext = ""
    n = 1
    Do While n <= Len(astr)
        c = AscW(Mid(astr, n, 1))
        If c < 0 Then
          c = c + 65536
        End If
        If c = 34 Or c = 39 Then ' substiitute ' and " with '
            utftext = utftext + "'"
        ElseIf c < 128 And c > 0 Then
            utftext = utftext + Chr(c)
        ElseIf ((c >= 128) And (c < 2048)) Then
            utftext = utftext + Chr(((c \ 64) Or 192))
            utftext = utftext + Chr(((c And 63) Or 128))
        ElseIf ((c >= 2048) And (c < 65536)) Then
            utftext = utftext + Chr(((c \ 4096) Or 224))
            utftext = utftext + Chr((((c \ 64) And 63) Or 128))
            utftext = utftext + Chr(((c And 63) Or 128))
        Else ' c >= 65536
            utftext = utftext + Chr(((c \ 262144) Or 240))
            utftext = utftext + Chr(((((c \ 4096) And 63)) Or 128))
            utftext = utftext + Chr((((c \ 64) And 63) Or 128))
            utftext = utftext + Chr(((c And 63) Or 128))
        End If
        n = n + 1
    Loop
    Encode_UTF8 = utftext
End Function

Sub checkDupplicate()

    Dim lig As Integer
    Dim val As String
    Dim stVal As String
    Dim lstErr As String
    stVal = ""
    lstErr = ""
    lig = 2
    While Cells(lig, 1) <> ""
        val = Cells(lig, 1)
        If val = stVal Then
            lstErr = lstErr & vbCrLf & "   => " & val
        End If
        stVal = val
        lig = lig + 1
    Wend
    
    If lstErr <> "" Then
        MsgBox "Dupplicate values for following data :" & lstErr & vbCrLf & vbCrLf & "Correct and retry."
        End
    End If
End Sub




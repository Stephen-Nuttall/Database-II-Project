package com.example.phaseIII.activities;

import android.util.Log;

import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RadioGroup;

import androidx.appcompat.app.AppCompatActivity;

import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.toolbox.Volley;
import com.example.PhaseIII.R;
import com.example.phaseIII.network.QueryRequest;

public class item1 extends AppCompatActivity {

    EditText etStudentID;
    EditText etEmail;
    EditText etOldPassword;
    EditText etNewPassword;
    EditText etName;
    RadioGroup radioGroup;
    Button submitQuery;

    String degree = "undergraduate";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.item1);

        etStudentID = findViewById(R.id.student_id);
        etEmail = findViewById(R.id.email);
        etOldPassword = findViewById(R.id.oldPassword);
        etNewPassword = findViewById(R.id.newPassword);
        etName = findViewById(R.id.name);
        radioGroup = findViewById(R.id.radioGroup);
        submitQuery = findViewById(R.id.submitQuery);

        degree = "undergraduate";

        radioGroup.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                // Find which radio button is selected
                if (checkedId == R.id.undergradOption) {
                    degree = "undergraduate";
                    Log.d("test", "Undergraduate selected");
                } else if (checkedId == R.id.masterOption) {
                    degree = "master";
                    Log.d("test", "Master selected");
                } else if (checkedId == R.id.phdOption) {
                    degree = "phd";
                    Log.d("test", "PhD selected");
                }
            }
        });

        submitQuery.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v){
                Log.d("test", "Button clicked!");
                String[] argNames = {
                        "student_id",
                        "email",
                        "current_password",
                        "new_password",
                        "name",
                        "degree",
                        "dept"
                };

                String[] args = {
                        etStudentID.getText().toString(),
                        etEmail.getText().toString(),
                        etOldPassword.getText().toString(),
                        etNewPassword.getText().toString(),
                        etName.getText().toString(),
                        degree,
                        "Miner School of Computer & Information Sciences"
                };

                Response.Listener<String> responseListener = new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
//                        try {
//                            Log.d("SubmitQueryHelp", response);
//                            JSONObject jsonResponse = new JSONObject(response);
//                            boolean success = jsonResponse.getBoolean("success");
//
//                            if(success){
//                                String title = jsonResponse.getString("title");
//                                int year = jsonResponse.getInt("year");
//                                int length = jsonResponse.getInt("length");
//                                String genre = jsonResponse.getString("genre");
//
//                                Intent intent = new Intent(MovieQuery.this, PageMovie.class);
//
//                                intent.putExtra("title", title);
//                                intent.putExtra("year", year);
//                                intent.putExtra("length", length);
//                                intent.putExtra("genre", genre);
//
//                                MovieQuery.this.startActivity(intent);
//                            } else{
//                                AlertDialog.Builder builder = new AlertDialog.Builder(MovieQuery.this);
//                                builder.setMessage("Sign In Failed").setNegativeButton("Retry", null).create().show();
//                            }
//                        } catch (JSONException e) {
//                            e.printStackTrace();
//                        }
                    }
                };

                QueryRequest queryRequest = new QueryRequest(argNames, args,getString(R.string.url) + "item1.php", responseListener);
                RequestQueue queue = Volley.newRequestQueue(item1.this);
                queue.add(queryRequest);
                Log.d("test", "Query sent");
            }

        });
    }
}

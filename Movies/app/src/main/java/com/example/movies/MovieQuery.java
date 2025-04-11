package com.example.movies;

import android.app.AlertDialog;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

import androidx.appcompat.app.AppCompatActivity;

import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.toolbox.Volley;

import org.json.JSONException;
import org.json.JSONObject;

public class MovieQuery extends AppCompatActivity {

    EditText etYear;
    Button submitQuery;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_movie_query);

        etYear = (EditText) findViewById(R.id.year);
        submitQuery = (Button) findViewById(R.id.submitQuery);

        submitQuery.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v){
                String year = etYear.getText().toString();

                Response.Listener<String> responseListener = new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
                        try {
                            Log.d("SubmitQueryHelp", response);
                            JSONObject jsonResponse = new JSONObject(response);
                            boolean success = jsonResponse.getBoolean("success");

                            if(success){
                                String title = jsonResponse.getString("title");
                                int year = jsonResponse.getInt("year");
                                int length = jsonResponse.getInt("length");
                                String genre = jsonResponse.getString("genre");

                                Intent intent = new Intent(MovieQuery.this, PageMovie.class);

                                intent.putExtra("title", title);
                                intent.putExtra("year", year);
                                intent.putExtra("length", length);
                                intent.putExtra("genre", genre);

                                MovieQuery.this.startActivity(intent);
                            } else{
                                AlertDialog.Builder builder = new AlertDialog.Builder(MovieQuery.this);
                                builder.setMessage("Sign In Failed").setNegativeButton("Retry", null).create().show();
                            }
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                    }
                };

                QueryRequest queryRequest = new QueryRequest(year,getString(R.string.url) + "mysql_example.php", responseListener);
                RequestQueue queue = Volley.newRequestQueue(MovieQuery.this);
                queue.add(queryRequest);
            }

        });
    }
}
